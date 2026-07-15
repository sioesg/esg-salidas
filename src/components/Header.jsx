import { useState } from "react";

function Header({
  pantalla,
  setSidebarAbierto,
  onLogout,
  usuario
}) {

  const [menuUsuario, setMenuUsuario] = useState(false);

  const obtenerTitulo = () => {

    switch (pantalla) {

      case "salidas":
        return "Registrar salida";

      case "historial":
        return "Historial de salidas";

      case "reportes":
        return "Reportes";

      default:
        return "Sistema de Salidas";

    }

  };

  const iniciales = usuario?.name
    ?.split(" ")
    .map((parte) => parte[0])
    .join("")
    .slice(0, 2)
    .toUpperCase() || "US";

  return (

    <header
      className="
        h-20
        bg-white
        border-b
        border-gray-200
        px-4
        md:px-6
        lg:px-8
        flex
        items-center
        justify-between
        shadow-sm
        shrink-0
      "
    >

      {/* IZQUIERDA */}
      <div className="flex items-center gap-4">

        <button
          onClick={() => setSidebarAbierto(true)}
          className="
            lg:hidden
            text-3xl
            text-slate-700
            hover:text-blue-600
            transition
          "
        >
          ☰
        </button>

        <div>

          <h2 className="text-xl md:text-2xl font-bold text-gray-800">
            {obtenerTitulo()}
          </h2>

          <p className="text-sm text-gray-500">
            Control de almacen
          </p>

        </div>

      </div>

      {/* USUARIO */}
      <div className="relative">

        <button
          onClick={() =>
            setMenuUsuario(!menuUsuario)
          }
          className="
            flex
            items-center
            gap-3
            hover:bg-gray-100
            px-3
            py-2
            rounded-xl
            transition
          "
        >

          <div
            className="
              w-10
              h-10
              md:w-12
              md:h-12
              rounded-full
              bg-blue-600
              flex
              items-center
              justify-center
              text-white
              font-bold
              shrink-0
            "
          >
            {iniciales}
          </div>

          <div className="hidden sm:block text-left">

            <p className="font-semibold text-gray-800">
              {usuario?.name || "Usuario"}
            </p>

            <p className="text-sm text-gray-500">
              {usuario?.role?.nombre || "Sin rol"}
            </p>

          </div>

        </button>

        {/* MENU DESPLEGABLE */}
        {menuUsuario && (

          <div
            className="
              absolute
              right-0
              top-16
              w-52
              bg-white
              rounded-xl
              shadow-lg
              border
              border-gray-200
              overflow-hidden
              z-50
            "
          >

            <button
              onClick={onLogout}
              className="
                w-full
                text-left
                px-4
                py-3
                text-red-600
                hover:bg-red-50
                transition
              "
            >
              Cerrar sesion
            </button>

          </div>

        )}

      </div>

    </header>

  );
}

export default Header;
