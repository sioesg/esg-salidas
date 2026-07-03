function Reportes() {

  return (

    <div className="space-y-6">
      
      {/* TARJETAS */}
      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <div className="bg-white rounded-2xl p-6 shadow-sm">

          <p className="text-gray-500 text-sm">
            Salidas totales
          </p>

          <h2 className="text-4xl font-bold text-blue-600 mt-2">
            128
          </h2>

        </div>

        <div className="bg-white rounded-2xl p-6 shadow-sm">

          <p className="text-gray-500 text-sm">
            Salidas este mes
          </p>

          <h2 className="text-4xl font-bold text-green-600 mt-2">
            34
          </h2>

        </div>

        <div className="bg-white rounded-2xl p-6 shadow-sm">

          <p className="text-gray-500 text-sm">
            Productos entregados
          </p>

          <h2 className="text-4xl font-bold text-orange-500 mt-2">
            452
          </h2>

        </div>

        <div className="bg-white rounded-2xl p-6 shadow-sm">

          <p className="text-gray-500 text-sm">
            Departamentos activos
          </p>

          <h2 className="text-4xl font-bold text-purple-600 mt-2">
            8
          </h2>

        </div>

      </div>

      {/* FILTROS */}
      <div className="bg-white rounded-2xl p-6 shadow-sm">

        <h2 className="text-xl font-semibold mb-5">
          Generar reporte
        </h2>

        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">

          <input
            type="text"
            placeholder="Buscar producto..."
            className="h-12 border border-gray-300 rounded-lg px-4"
          />

          <input
            type="date"
            className="h-12 border border-gray-300 rounded-lg px-4"
          />

          <input
            type="date"
            className="h-12 border border-gray-300 rounded-lg px-4"
          />

          <select
            className="h-12 border border-gray-300 rounded-lg px-4"
          >
            <option>Todos los departamentos</option>
            <option>Operaciones</option>
            <option>Taller</option>
            <option>Administración</option>
          </select>

          <button
            className="
            bg-blue-600
            hover:bg-blue-700
            text-white
            rounded-lg
            px-4
            "
          >
            Generar reporte
          </button>

        </div>

      </div>

      {/* PRODUCTOS MÁS UTILIZADOS */}
      <div className="bg-white rounded-2xl p-6 shadow-sm">

        <h2 className="text-xl font-semibold mb-5">
          Productos más utilizados
        </h2>

        <div className="overflow-x-auto">

          <table className="w-full">

            <thead className="bg-slate-900 text-white">

              <tr>

                <th className="p-4 text-left">
                  Producto
                </th>

                <th className="p-4 text-left">
                  Cantidad entregada
                </th>

                <th className="p-4 text-left">
                  Departamento principal
                </th>

              </tr>

            </thead>

            <tbody>

              <tr className="border-b">

                <td className="p-4">
                  Aceite Hidráulico
                </td>

                <td className="p-4">
                  120
                </td>

                <td className="p-4">
                  Taller
                </td>

              </tr>

              <tr className="border-b">

                <td className="p-4">
                  Filtro de Aire
                </td>

                <td className="p-4">
                  98
                </td>

                <td className="p-4">
                  Operaciones
                </td>

              </tr>

              <tr>

                <td className="p-4">
                  Anticongelante
                </td>

                <td className="p-4">
                  76
                </td>

                <td className="p-4">
                  Taller
                </td>

              </tr>

            </tbody>

          </table>

        </div>

      </div>

      {/* EXPORTACIÓN */}
      <div className="bg-white rounded-2xl p-6 shadow-sm">

        <h2 className="text-xl font-semibold mb-5">
          Exportar información
        </h2>

        <div className="flex flex-col md:flex-row gap-4">

          <button
            className="
            bg-green-600
            hover:bg-green-700
            text-white
            px-6
            py-3
            rounded-lg
            "
          >
            Exportar Excel
          </button>

          <button
            className="
            bg-red-600
            hover:bg-red-700
            text-white
            px-6
            py-3
            rounded-lg
            "
          >
            Exportar PDF
          </button>

        </div>

      </div>

    </div>

  );
}

export default Reportes;