import { useState } from "react";

function Historial() {

  const [detalleSalida, setDetalleSalida] = useState(null);

  const salidas = [
    {
      folio: "SAL-0001",
      fecha: "01/06/2026",
      usuario: "Juan Pérez",
      departamento: "Operaciones",
      unidad: "Unidad 01",
      observaciones: "Cambio de uniformes",
      productos: [
        {
          producto: "Zapatos de seguridad",
          cantidad: 3,
        },
        {
          producto: "Chalecos reflejantes",
          cantidad: 4,
        },
      ],
    },
    {
      folio: "SAL-0002",
      fecha: "01/06/2026",
      usuario: "Pedro López",
      departamento: "Taller",
      unidad: "Unidad 15",
      observaciones: "Mantenimiento preventivo",
      productos: [
        {
          producto: "Aceite hidráulico",
          cantidad: 10,
        },
      ],
    },
    {
      folio: "SAL-0003",
      fecha: "02/06/2026",
      usuario: "María Sánchez",
      departamento: "Administración",
      unidad: "-",
      observaciones: "Pendiente de autorización",
      productos: [
        {
          producto: "Camisas",
          cantidad: 10,
        },
      ],
    },
  ];

  return (

    <div className="p-6 flex flex-col gap-6">

      {/* FILTROS */}
      <div className="bg-white rounded-2xl p-6 shadow-sm">

        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

          <input
            type="text"
            placeholder="Buscar folio..."
            className="
              h-12
              border
              border-gray-300
              rounded-lg
              px-4
              focus:outline-none
              focus:ring-2
              focus:ring-blue-500
            "
          />

           <input
            type="text"
            placeholder="Producto..."
            className="
              h-12
              border
              border-gray-300
              rounded-lg
              px-4
              focus:outline-none
              focus:ring-2
              focus:ring-blue-500
            "
          />

          <input
            type="date"
            className="
              h-12
              border
              border-gray-300
              rounded-lg
              px-4
              focus:outline-none
              focus:ring-2
              focus:ring-blue-500
            "
          />

           <input
            type="date"
            className="
              h-12
              border
              border-gray-300
              rounded-lg
              px-4
              focus:outline-none
              focus:ring-2
              focus:ring-blue-500
            "
          />

          <select
            className="
              h-12
              border
              border-gray-300
              rounded-lg
              px-4
              focus:outline-none
              focus:ring-2
              focus:ring-blue-500
            "
          >
            <option>Todos los departamentos</option>
            <option>Operaciones</option>
            <option>Taller</option>
            <option>Administración</option>
          </select>

          <button
            className="
              h-12
              bg-blue-600
              hover:bg-blue-700
              text-white
              rounded-lg
              font-medium
            "
          >
            Buscar
          </button>

        </div>

      </div>

      {/* TABLA */}
      <div className="bg-white rounded-2xl shadow-sm overflow-hidden">

        <table className="w-full table-auto">

          <thead className="bg-slate-900 text-white">

            <tr>

              <th className="text-left p-4">
                Folio
              </th>

              <th className="text-left p-4">
                Fecha
              </th>

              <th className="text-left p-4">
                Usuario
              </th>

              <th className="hidden lg:table-cell text-left p-4">
                Departamento
              </th>

              <th className="hidden xl:table-cell text-left p-4">
                Unidad
              </th>

              <th className="text-center p-4">
                Acciones
              </th>

            </tr>

          </thead>

          <tbody>

            {salidas.map((salida, index) => (

              <tr
                key={index}
                className="
                  border-b
                  border-gray-200
                  hover:bg-gray-50
                  transition
                "
              >

                <td className="p-4 font-semibold">
                  {salida.folio}
                </td>

                <td className="p-4">
                  {salida.fecha}
                </td>

                <td className="p-4">
                  {salida.usuario}
                </td>

                <td className="hidden lg:table-cell p-4">
                  {salida.departamento}
                </td>

                <td className="hidden xl:table-cell p-4">
                  {salida.unidad}
                </td>

                <td className="p-4 text-center">

                  <button
                    onClick={() =>
                      setDetalleSalida(salida)
                    }
                    className="
                      bg-slate-900
                      hover:bg-slate-700
                      text-white
                      px-4
                      py-2
                      rounded-lg
                      text-sm
                      transition
                    "
                  >
                    Ver detalle
                  </button>

                </td>

              </tr>

            ))}

          </tbody>

        </table>

      </div>

      {/* PAGINACIÓN */}
      <div className="bg-white rounded-2xl p-4 shadow-sm">

        <div className="flex justify-between items-center">

          <span className="text-sm text-gray-500">
            Mostrando 1 - 3 de 3 registros
          </span>

          <div className="flex gap-2">

            <button
              className="
                border
                border-gray-300
                px-4
                py-2
                rounded-lg
                hover:bg-gray-100
              "
            >
              Anterior
            </button>

            <button
              className="
                bg-blue-600
                text-white
                px-4
                py-2
                rounded-lg
              "
            >
              1
            </button>

            <button
              className="
                border
                border-gray-300
                px-4
                py-2
                rounded-lg
                hover:bg-gray-100
              "
            >
              Siguiente
            </button>

          </div>

        </div>

      </div>

      {/* MODAL */}
      {detalleSalida && (

        <div
          className="
            fixed
            inset-0
            bg-black/50
            flex
            items-center
            justify-center
            z-50
            p-4
          "
        >

          <div
            className="
              bg-white
              rounded-2xl
              w-full
              max-w-3xl
              shadow-xl
              overflow-hidden
            "
          >

            <div className="bg-slate-900 text-white p-6">

              <h2 className="text-2xl font-bold">
                {detalleSalida.folio}
              </h2>

              <p className="text-gray-300">
                Detalle de salida
              </p>

            </div>

            <div className="p-6 space-y-6">

              <div className="grid md:grid-cols-2 gap-6">

                <div>
                  <p className="font-semibold">
                    Fecha
                  </p>
                  <p>{detalleSalida.fecha}</p>
                </div>

                <div>
                  <p className="font-semibold">
                    Usuario
                  </p>
                  <p>{detalleSalida.usuario}</p>
                </div>

                <div>
                  <p className="font-semibold">
                    Departamento
                  </p>
                  <p>{detalleSalida.departamento}</p>
                </div>

                <div>
                  <p className="font-semibold">
                    Unidad
                  </p>
                  <p>{detalleSalida.unidad}</p>
                </div>

              </div>

              <div>

                <h3 className="font-bold text-lg mb-3">
                  Productos
                </h3>

                <div className="border rounded-lg overflow-hidden">

                  {detalleSalida.productos.map(
                    (producto, index) => (

                      <div
                        key={index}
                        className="
                          flex
                          justify-between
                          p-4
                          border-b
                        "
                      >

                        <span>
                          {producto.producto}
                        </span>

                        <span className="font-semibold">
                          {producto.cantidad}
                        </span>

                      </div>

                    )
                  )}

                </div>

              </div>

              <div>

                <h3 className="font-bold text-lg mb-2">
                  Observaciones
                </h3>

                <p className="text-gray-600">
                  {detalleSalida.observaciones}
                </p>

              </div>

            </div>

            <div className="p-6 border-t flex justify-end">

              <button
                onClick={() =>
                  setDetalleSalida(null)
                }
                className="
                  bg-blue-600
                  hover:bg-blue-700
                  text-white
                  px-6
                  py-3
                  rounded-lg
                "
              >
                Cerrar
              </button>

            </div>

          </div>

        </div>

      )}

    </div>

  );
}

export default Historial;