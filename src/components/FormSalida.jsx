import { useState } from "react";

function FormSalida() {

  const [departamento, setDepartamento] = useState("");

  const [esUnidad, setEsUnidad] = useState(false);

  const [modoEscaneo, setModoEscaneo] = useState(false);

  const [producto, setProducto] = useState("");

  const [cantidad, setCantidad] = useState("");

  const [productos, setProductos] = useState([]);


  const catalogoProductos = [

    {
      id: 1,
      nombre: "Zapato industrial",
      unidadMedida: "Pieza",
      existencia: 25,
    },

    {
      id: 2,
      nombre: "Aceite ",
      unidadMedida: "Litros",
      existencia: 60,
    },

    {
      id: 3,
      nombre: "Camisa uniforme",
      unidadMedida: "Pieza",
      existencia: 40,
    },

    {
      id: 4,
      nombre: "Casco de seguridad",
      unidadMedida: "Pieza",
      existencia: 18,
    },

  ];

  const productoSeleccionado =
    catalogoProductos.find(
      (p) => p.nombre === producto
    );

  const agregarProducto = () => {

    if (
      producto === "" ||
      cantidad === ""
    ) {

      alert(
        "Selecciona un producto y una cantidad"
      );

      return;

    }

    setProductos([

      ...productos,

      {

        producto,

        cantidad,

        unidadMedida:
          productoSeleccionado.unidadMedida,

        existencia:
          productoSeleccionado.existencia,

      },

    ]);

    setProducto("");

    setCantidad("");

  };

  const eliminarProducto = (index) => {

    setProductos(

      productos.filter(
        (_, i) => i !== index
      )

    );

  };

  return (

    <div className="flex-1 bg-white rounded-2xl shadow-sm p-6 lg:p-8 pb-32">

      {/* DATOS GENERALES */}

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {/* FECHA */}

        <div>

          <label className="block mb-3 text-sm font-semibold text-gray-700">

            Fecha

          </label>

          <input

            type="date"

            className="
              w-full
              h-14
              border
              border-gray-300
              rounded-lg
              px-4
              focus:outline-none
              focus:ring-2
              focus:ring-blue-500
            "

          />

        </div>

        {/* USUARIO */}

        <div>

          <label className="block mb-3 text-sm font-semibold text-gray-700">

            Usuario

          </label>

          <input

            type="text"

            placeholder="Nombre del usuario"

            className="
              w-full
              h-14
              border
              border-gray-300
              rounded-lg
              px-4
              focus:outline-none
              focus:ring-2
              focus:ring-blue-500
            "

          />

        </div>

        {/* DEPARTAMENTO */}

        <div>

          <label className="block mb-3 text-sm font-semibold text-gray-700">

            Departamento

          </label>

          <select

            value={departamento}

            onChange={(e) => {

              setDepartamento(
                e.target.value
              );

              if (

                e.target.value !==
                  "Operaciones" &&

                e.target.value !==
                  "Taller"

              ) {

                setEsUnidad(false);

              }

            }}

            className="
              w-full
              h-14
              border
              border-gray-300
              rounded-lg
              px-4
              bg-white
              focus:outline-none
              focus:ring-2
              focus:ring-blue-500
            "

          >

            <option value="">

              Selecciona un departamento

            </option>

            <option value="Operaciones">

              Operaciones

            </option>

            <option value="Taller">

              Taller

            </option>

            <option value="Administración">

              Administración

            </option>

          </select>

          {(departamento ===
            "Operaciones" ||

            departamento ===
              "Taller") && (

            <div className="mt-4">

              <div className="flex items-center gap-3">

                <input

                  type="checkbox"

                  checked={esUnidad}

                  onChange={() =>
                    setEsUnidad(
                      !esUnidad
                    )
                  }

                  className="
                    w-5
                    h-5
                    accent-blue-600
                  "

                />

                <label className="text-sm font-medium text-gray-700">

                  ¿Es para una unidad?

                </label>

              </div>

              {esUnidad && (

                <div className="mt-4">

                  <select

                    className="
                      w-full
                      h-14
                      border
                      border-gray-300
                      rounded-lg
                      px-4
                      bg-white
                      focus:outline-none
                      focus:ring-2
                      focus:ring-blue-500
                    "

                  >

                    <option>

                      Selecciona una unidad

                    </option>

                    <option>Unidad 01</option>

                    <option>Unidad 02</option>

                    <option>Unidad 03</option>

                    <option>Unidad 04</option>

                    <option>Unidad 05</option>

                    <option>Unidad 10</option>

                    <option>Unidad 15</option>

                    <option>Unidad 20</option>

                  </select>

                </div>

              )}

            </div>

          )}

        </div>

        {/* TIPO */}

        <div>

          <label className="block mb-3 text-sm font-semibold text-gray-700">

            Tipo de salida

          </label>

          <select

            className="
              w-full
              h-14
              border
              border-gray-300
              rounded-lg
              px-4
              bg-white
              focus:outline-none
              focus:ring-2
              focus:ring-blue-500
            "

          >

            <option>

              Consumo interno

            </option>

            <option>

              Mantenimiento

            </option>

          </select>

        </div>

      </div>

      {/* PRODUCTOS */}

      <div className="mt-12">

        <h4 className="text-2xl font-bold text-gray-800 mb-6">

          Detalle de productos

        </h4>

        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-4">

          {/* PRODUCTO */}

          <div className="xl:col-span-4">

            <label className="block mb-3 text-sm font-semibold text-gray-700">

              Producto

            </label>

            <select

              value={producto}

              onChange={(e) =>
                setProducto(
                  e.target.value
                )
              }

              className="
                w-full
                h-14
                border
                border-gray-300
                rounded-lg
                px-4
                bg-white
                focus:outline-none
                focus:ring-2
                focus:ring-blue-500
              "

            >

              <option value="">

                Selecciona un producto

              </option>

              {catalogoProductos.map(
                (item) => (

                  <option

                    key={item.id}

                    value={item.nombre}

                  >

                    {item.nombre}

                  </option>

                )
              )}

            </select>

          </div>

          {/* UNIDAD */}

          <div className="xl:col-span-2">

            <label className="block mb-3 text-sm font-semibold text-gray-700">

              Unidad

            </label>

            <input

              disabled

              value={
                productoSeleccionado
                  ?.unidadMedida || ""
              }

              className="
                w-full
                h-14
                border
                border-gray-300
                rounded-lg
                px-4
                bg-gray-100
              "

            />

          </div>

          {/* EXISTENCIA */}

          <div className="xl:col-span-2">

            <label className="block mb-3 text-sm font-semibold text-gray-700">

              Existencia

            </label>

            <input

              disabled

              value={
                productoSeleccionado
                  ?.existencia || ""
              }

              className="
                w-full
                h-14
                border
                border-gray-300
                rounded-lg
                px-4
                bg-gray-100
              "

            />

          </div>

          {/* CANTIDAD */}

          <div className="xl:col-span-2">

            <label className="block mb-3 text-sm font-semibold text-gray-700">

              Cantidad

            </label>

            <input

              type="number"

              value={cantidad}

              onChange={(e) =>
                setCantidad(
                  e.target.value
                )
              }

              placeholder="0"

              className="
                w-full
                h-14
                border
                border-gray-300
                rounded-lg
                px-4
                focus:outline-none
                focus:ring-2
                focus:ring-blue-500
              "

            />

          </div>

        {/* BOTONES */}
        <div className="xl:col-span-12">

          <div className="flex flex-wrap gap-3 mt-2">

            <button
              type="button"
              onClick={() =>
                setModoEscaneo(!modoEscaneo)
              }
              className={`
                h-14
                px-6
                rounded-lg
                font-medium
                transition

                ${
                  modoEscaneo
                    ? "bg-green-600 text-white hover:bg-green-700"
                    : "bg-slate-900 text-white hover:bg-slate-700"
                }
              `}
            >

              Escanear producto

            </button>

            <button
              type="button"
              onClick={agregarProducto}
              className="
                h-14
                px-8
                bg-blue-600
                hover:bg-blue-700
                text-white
                rounded-lg
                font-semibold
                transition
              "
            >

              Agregar producto

            </button>

          </div>

        </div>

        </div>

      </div>

      {/* TABLA */}

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

                  <th className="p-4 text-left">

                    Producto
                  </th>

                  <th className="p-4 text-left">

                    Unidad

                  </th>

                  <th className="p-4 text-left">

                    Existencia

                  </th>

                  <th className="p-4 text-left">

                    Cantidad

                  </th>

                  <th className="p-4 text-center">

                    Acción

                  </th>

                </tr>

              </thead>

              <tbody>

                {productos.map(
                  (item, index) => (

                    <tr
                      key={index}
                      className="border-t"
                    >

                      <td className="p-4">

                        {item.producto}

                      </td>

                      <td className="p-4">

                        {item.unidadMedida}

                      </td>

                      <td className="p-4">

                        {item.existencia}

                      </td>

                      <td className="p-4">

                        {item.cantidad}

                      </td>

                      <td className="p-4 text-center">

                        <button

                          onClick={() =>
                            eliminarProducto(
                              index
                            )
                          }

                          className="
                            text-red-600
                            hover:text-red-800
                            font-medium
                          "

                        >

                          Eliminar

                        </button>

                      </td>

                    </tr>

                  )
                )}

              </tbody>

            </table>

          </div>

        )}

      </div>

      {/* OBSERVACIONES */}

      <div className="mt-10">

        <label className="block mb-3 text-sm font-semibold text-gray-700">

          Observaciones

        </label>

        <textarea

          placeholder="Agregar observaciones..."

          className="
            w-full
            min-h-[180px]
            border
            border-gray-300
            rounded-lg
            px-4
            py-4
            resize-none
            focus:outline-none
            focus:ring-2
            focus:ring-blue-500
          "

        />

      </div>

      {/* BOTONES FLOTANTES */}

      <div className="fixed bottom-6 right-6 z-50 flex gap-3">

        <button

          className="
            bg-white
            border
            border-gray-300
            px-6
            py-3
            rounded-xl
            shadow-md
          "

        >

          Limpiar

        </button>

        <button

          className="
            bg-blue-600
            hover:bg-blue-700
            text-white
            px-8
            py-3
            rounded-xl
            shadow-lg
            font-semibold
          "

        >

          Registrar salida

        </button>

      </div>

    </div>

  );

}

export default FormSalida;