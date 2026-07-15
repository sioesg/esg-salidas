import { useEffect, useState } from "react";

import { obtenerDepartamentos } from "../services/departamentosApi";
import { obtenerProducto, obtenerProductos } from "../services/productosApi";
import { registrarSalida } from "../services/salidasApi";
import { obtenerUnidades } from "../services/unidadesApi";
import PruebaEscaner from "./PruebaEscaner";

function FormSalida() {
  const [usuarioNombre, setUsuarioNombre] = useState("");
  const [clienteCodigo, setClienteCodigo] = useState("");
  const [clienteNombre, setClienteNombre] = useState("");
  const [departamentos, setDepartamentos] = useState([]);
  const [unidades, setUnidades] = useState([]);
  const [esUnidad, setEsUnidad] = useState(false);
  const [referencia, setReferencia] = useState("");
  const [unidadVehicularId, setUnidadVehicularId] = useState("");
  const [modoEscaneo, setModoEscaneo] = useState(false);
  const [producto, setProducto] = useState("");
  const [cantidad, setCantidad] = useState("");
  const [productos, setProductos] = useState([]);
  const [catalogoProductos, setCatalogoProductos] = useState([]);
  const [productoSeleccionado, setProductoSeleccionado] = useState(null);
  const [observaciones, setObservaciones] = useState("");
  const [cargandoProductos, setCargandoProductos] = useState(true);
  const [cargandoDepartamentos, setCargandoDepartamentos] = useState(true);
  const [cargandoProducto, setCargandoProducto] = useState(false);
  const [cargandoUnidades, setCargandoUnidades] = useState(true);
  const [enviando, setEnviando] = useState(false);
  const [mensaje, setMensaje] = useState("");
  const [error, setError] = useState("");
  const [resultadoSalida, setResultadoSalida] = useState(null);

  useEffect(() => {
    let cancelado = false;

    const cargarProductos = async () => {
      setCargandoProductos(true);

      try {
        const productosNormalizados = await obtenerProductos();

        if (!cancelado) {
          setCatalogoProductos(Array.isArray(productosNormalizados) ? productosNormalizados : []);
        }
      } catch (error) {
        console.error("Error cargando productos", error);

        if (!cancelado) {
          setCatalogoProductos([]);
          setError("No fue posible cargar productos");
        }
      } finally {
        if (!cancelado) {
          setCargandoProductos(false);
        }
      }
    };

    const cargarDepartamentos = async () => {
      setCargandoDepartamentos(true);

      try {
        const departamentosNormalizados = await obtenerDepartamentos();

        if (!cancelado) {
          const listaDepartamentos = Array.isArray(departamentosNormalizados)
            ? departamentosNormalizados
            : [];

          setDepartamentos(listaDepartamentos);

          if (listaDepartamentos.length === 1) {
            setClienteCodigo(listaDepartamentos[0].codigo);
            setClienteNombre(listaDepartamentos[0].nombre);
          }
        }
      } catch (error) {
        console.error("Error cargando departamentos", error);

        if (!cancelado) {
          setDepartamentos([]);
          setError("No fue posible cargar departamentos");
        }
      } finally {
        if (!cancelado) {
          setCargandoDepartamentos(false);
        }
      }
    };

    const cargarUnidades = async () => {
      setCargandoUnidades(true);

      try {
        const unidadesNormalizadas = await obtenerUnidades();

        if (!cancelado) {
          setUnidades(Array.isArray(unidadesNormalizadas) ? unidadesNormalizadas : []);
        }
      } catch (error) {
        console.error("Error cargando unidades vehiculares", error);

        if (!cancelado) {
          setUnidades([]);
        }
      } finally {
        if (!cancelado) {
          setCargandoUnidades(false);
        }
      }
    };

    cargarProductos();
    cargarDepartamentos();
    cargarUnidades();

    return () => {
      cancelado = true;
    };
  }, []);

  useEffect(() => {
    if (producto === "") {
      return;
    }

    if (!Array.isArray(catalogoProductos) || catalogoProductos.length === 0) {
      return;
    }

    let cancelado = false;

    const cargarProducto = async () => {
      setCargandoProducto(true);
      setError("");

      try {
        const productoDetalle = await obtenerProducto(producto);

        if (cancelado) {
          return;
        }

        if (!productoDetalle?.id || !productoDetalle?.codigo || !productoDetalle?.nombre) {
          console.error("Producto normalizado invalido", productoDetalle);
          setProductoSeleccionado(null);
          setError("El producto seleccionado no tiene datos completos");
          return;
        }

        setProductoSeleccionado({
          id: productoDetalle.id,
          codigo: productoDetalle.codigo,
          nombre: productoDetalle.nombre,
          cantidad: 0,
          existencia: productoDetalle.existencia,
        });
      } catch (error) {
        console.error("Error cargando informacion del producto", error);

        if (!cancelado) {
          setProductoSeleccionado(null);
          setError(error.message || "No fue posible cargar la informacion del producto");
        }
      } finally {
        if (!cancelado) {
          setCargandoProducto(false);
        }
      }
    };

    cargarProducto();

    return () => {
      cancelado = true;
    };
  }, [producto, catalogoProductos]);

  const departamentoSeleccionado = Array.isArray(departamentos)
    ? departamentos.find((item) => item.codigo === clienteCodigo)
    : null;

  const unidadSeleccionada = Array.isArray(unidades)
    ? unidades.find((item) => String(item.id) === String(unidadVehicularId))
    : null;

  const agregarProducto = () => {
    const cantidadNumero = Number(cantidad);

    if (!productoSeleccionado) {
      setError("Selecciona un producto valido");
      return;
    }

    if (!Number.isFinite(cantidadNumero) || cantidadNumero <= 0) {
      setError("Captura una cantidad mayor a cero");
      return;
    }

    if (!productoSeleccionado.id || !productoSeleccionado.codigo || !productoSeleccionado.nombre) {
      console.error("Producto seleccionado incompleto", productoSeleccionado);
      setError("El producto seleccionado no tiene datos completos");
      return;
    }

    const nuevoProducto = {
      id: productoSeleccionado.id,
      codigo: productoSeleccionado.codigo,
      nombre: productoSeleccionado.nombre,
      existencia: productoSeleccionado.existencia ?? 0,
      cantidad: cantidadNumero,
      observaciones: "",
    };

    setProductos((prev) => [
      ...(Array.isArray(prev) ? prev : []),
      nuevoProducto,
    ]);

    setProducto("");
    setCantidad("");
    setProductoSeleccionado(null);
    setError("");
  };

  const eliminarProducto = (index) => {
    setProductos((prev) => (Array.isArray(prev) ? prev.filter((_, i) => i !== index) : []));
  };

  const limpiarFormulario = ({ conservarMensaje = false } = {}) => {
    setUsuarioNombre("");
    setClienteCodigo(departamentos.length === 1 ? departamentos[0].codigo : "");
    setClienteNombre(departamentos.length === 1 ? departamentos[0].nombre : "");
    setEsUnidad(false);
    setReferencia("");
    setUnidadVehicularId("");
    setProducto("");
    setCantidad("");
    setProductos([]);
    setProductoSeleccionado(null);
    setObservaciones("");
    setError("");

    if (!conservarMensaje) {
      setMensaje("");
    }
  };

  const registrar = async () => {
    setMensaje("");
    setError("");

    if (usuarioNombre.trim() === "") {
      setError("Captura el usuario relacionado con la salida");
      return;
    }

    if (!departamentoSeleccionado) {
      setError("Selecciona un departamento configurado");
      return;
    }

    if (!Array.isArray(productos) || productos.length === 0) {
      setError("Agrega al menos un producto");
      return;
    }

    if (productos.some((item) => !item?.codigo || !Number.isFinite(Number(item.cantidad)) || Number(item.cantidad) <= 0)) {
      setError("Hay productos incompletos o con cantidad invalida");
      return;
    }

    if (esUnidad && !unidadSeleccionada) {
      setError("Selecciona una unidad vehicular valida");
      return;
    }

    setEnviando(true);

    try {
      const salida = await registrarSalida({
        usuario_nombre: usuarioNombre.trim(),
        cliente_codigo: departamentoSeleccionado.codigo,
        cliente_nombre: clienteNombre || departamentoSeleccionado.nombre,
        referencia: esUnidad ? referencia : "",
        unidad_vehicular: esUnidad ? Number(unidadSeleccionada.id) : null,
        observaciones,
        productos,
      });

      setResultadoSalida(salida.data || salida);
      setMensaje("Salida registrada correctamente");
      limpiarFormulario({ conservarMensaje: true });
    } catch (error) {
      console.error("Error registrando salida en CONTPAQi", error);
      setError(error.data?.message || error.message || "No fue posible registrar la salida en CONTPAQi");
    } finally {
      setEnviando(false);
    }
  };

  return (
    <div className="flex-1 bg-white rounded-2xl shadow-sm p-6 lg:p-8 pb-32">
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
          <label className="block mb-3 text-sm font-semibold text-gray-700">
            Fecha
          </label>
          <input type="date" className="w-full h-14 border border-gray-300 rounded-lg px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label className="block mb-3 text-sm font-semibold text-gray-700">
            Solicitante
          </label>
          <input
            type="text"
            value={usuarioNombre}
            onChange={(e) => setUsuarioNombre(e.target.value)}
            placeholder="Nombre del solicitante"
            className="w-full h-14 border border-gray-300 rounded-lg px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label className="block mb-3 text-sm font-semibold text-gray-700">
            Departamento
          </label>
          <select
            value={clienteCodigo}
            onChange={(e) => {
              const codigo = e.target.value;
              const seleccionado = departamentos.find((item) => item.codigo === codigo);

              setClienteCodigo(codigo);
              setClienteNombre(seleccionado?.nombre || "");
              setEsUnidad(false);
              setReferencia("");
              setUnidadVehicularId("");
            }}
            className="w-full h-14 border border-gray-300 rounded-lg px-4 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">
              {cargandoDepartamentos ? "Cargando departamentos..." : "Selecciona un departamento"}
            </option>
            {departamentos.map((item) => (
              <option key={item.id ?? item.codigo} value={item.codigo}>
                {item.nombre}
              </option>
            ))}
          </select>

          <div className="mt-4">
            <div className="flex items-center gap-3">
              <input
                type="checkbox"
                checked={esUnidad}
                onChange={() => {
                  setEsUnidad(!esUnidad);
                  if (esUnidad) {
                    setReferencia("");
                    setUnidadVehicularId("");
                  }
                }}
                className="w-5 h-5 accent-blue-600"
              />
              <label className="text-sm font-medium text-gray-700">
                Es para una unidad?
              </label>
            </div>

            {esUnidad && (
              <div className="mt-4">
                <select
                  value={unidadVehicularId}
                  onChange={(e) => {
                    const id = e.target.value;
                    const seleccionada = unidades.find((item) => String(item.id) === String(id));

                    setUnidadVehicularId(id);
                    setReferencia(seleccionada?.numero || "");
                  }}
                  disabled={cargandoUnidades}
                  className="w-full h-14 border border-gray-300 rounded-lg px-4 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">
                    {cargandoUnidades ? "Cargando unidades..." : "Selecciona una unidad"}
                  </option>
                  {unidades.map((item) => (
                    <option key={`${item.id}-${item.contpaq_id ?? item.numero}`} value={item.id}>
                      {item.numero} - {item.placas || item.nombre}
                    </option>
                  ))}
                </select>
              </div>
            )}
          </div>
        </div>
      </div>

      <div className="mt-12">
        <h4 className="text-2xl font-bold text-gray-800 mb-6">
          Detalle de productos
        </h4>

        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-4">
          <div className="xl:col-span-4">
            <label className="block mb-3 text-sm font-semibold text-gray-700">
              Producto
            </label>
            <select
              value={producto}
              onChange={(e) => {
                const value = e.target.value;

                setProducto(value);

                if (value === "") {
                  setProductoSeleccionado(null);
                  setCargandoProducto(false);
                }
              }}
              disabled={cargandoProductos}
              className="w-full h-14 border border-gray-300 rounded-lg px-4 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">
                {cargandoProductos ? "Cargando productos..." : "Selecciona un producto"}
              </option>
              {catalogoProductos.map((item) => (
                <option key={item.id} value={item.id}>
                  {item.codigo} - {item.nombre}
                </option>
              ))}
            </select>
          </div>

          <div className="xl:col-span-2">
            <label className="block mb-3 text-sm font-semibold text-gray-700">
              Codigo
            </label>
            <input disabled value={cargandoProducto ? "Cargando..." : (productoSeleccionado?.codigo ?? "")} className="w-full h-14 border border-gray-300 rounded-lg px-4 bg-gray-100" />
          </div>

          <div className="xl:col-span-2">
            <label className="block mb-3 text-sm font-semibold text-gray-700">
              Existencia
            </label>
            <input disabled value={cargandoProducto ? "Cargando..." : (productoSeleccionado?.existencia ?? "")} className="w-full h-14 border border-gray-300 rounded-lg px-4 bg-gray-100" />
          </div>

          <div className="xl:col-span-2">
            <label className="block mb-3 text-sm font-semibold text-gray-700">
              Cantidad
            </label>
            <input type="number" value={cantidad} onChange={(e) => setCantidad(e.target.value)} placeholder="0" className="w-full h-14 border border-gray-300 rounded-lg px-4 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>

          <div className="xl:col-span-12">
            <div className="flex flex-wrap gap-3 mt-2">
              <button type="button" onClick={() => setModoEscaneo(!modoEscaneo)} className={`h-14 px-6 rounded-lg font-medium transition ${modoEscaneo ? "bg-green-600 text-white hover:bg-green-700" : "bg-slate-900 text-white hover:bg-slate-700"}`}>
                Escanear producto
              </button>

              <button type="button" onClick={agregarProducto} className="h-14 px-8 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                Agregar producto
              </button>
            </div>

            <PruebaEscaner />
          </div>
        </div>

        {error && (
          <p className="mt-2 text-sm text-red-600">
            {error}
          </p>
        )}

        {mensaje && (
          <p className="mt-2 text-sm text-green-600">
            {mensaje}
          </p>
        )}
      </div>

      <div className="mt-8 border rounded-xl overflow-hidden">
        <div className="bg-slate-900 text-white px-6 py-4 font-semibold">
          Productos agregados
        </div>

        {productos.length === 0 ? (
          <div className="p-8 text-center text-gray-500">
            No hay productos agregados
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-100">
                  <th className="p-4 text-left">Producto</th>
                  <th className="p-4 text-left">Codigo</th>
                  <th className="p-4 text-left">Existencia</th>
                  <th className="p-4 text-left">Cantidad</th>
                  <th className="p-4 text-center">Accion</th>
                </tr>
              </thead>
              <tbody>
                {productos.filter(Boolean).map((item, index) => (
                  <tr key={`${item.id}-${index}`} className="border-t">
                    <td className="p-4">{item.nombre}</td>
                    <td className="p-4">{item.codigo}</td>
                    <td className="p-4">{item.existencia}</td>
                    <td className="p-4">{item.cantidad}</td>
                    <td className="p-4 text-center">
                      <button onClick={() => eliminarProducto(index)} className="text-red-600 hover:text-red-800 font-medium">
                        Eliminar
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      <div className="mt-10">
        <label className="block mb-3 text-sm font-semibold text-gray-700">
          Observaciones
        </label>
        <textarea value={observaciones} onChange={(e) => setObservaciones(e.target.value)} placeholder="Agregar observaciones..." className="w-full min-h-[180px] border border-gray-300 rounded-lg px-4 py-4 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <div className="fixed bottom-6 right-6 z-50 flex gap-3">
        <button type="button" onClick={() => limpiarFormulario()} className="bg-white border border-gray-300 px-6 py-3 rounded-xl shadow-md">
          Limpiar
        </button>

        <button type="button" disabled={enviando} onClick={registrar} className="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl shadow-lg font-semibold">
          {enviando ? "Registrando..." : "Registrar salida"}
        </button>
      </div>

      {resultadoSalida && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-2xl w-full max-w-md shadow-xl overflow-hidden">
            <div className="bg-slate-900 text-white p-6">
              <h2 className="text-2xl font-bold">Salida registrada correctamente</h2>
              <p className="text-gray-300">Documento registrado en CONTPAQi</p>
            </div>

            <div className="p-6 space-y-4">
              <div>
                <p className="text-sm font-semibold text-gray-500">Folio CONTPAQi</p>
                <p className="text-xl font-bold text-gray-900">{resultadoSalida.folio_contpaq || "No incluido en la respuesta"}</p>
              </div>

              {resultadoSalida.contpaq_documento_id && (
                <div>
                  <p className="text-lg font-semibold text-gray-900">{resultadoSalida.contpaq_documento_id}</p>
                </div>
              )}
            </div>

            <div className="p-6 border-t flex justify-end">
              <button
                type="button"
                onClick={() => setResultadoSalida(null)}
                className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg"
              >
                Aceptar
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default FormSalida;
