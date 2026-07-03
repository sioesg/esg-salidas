function Sidebar({
  pantalla,
  setPantalla,
  sidebarAbierto,
  setSidebarAbierto,
  onLogout
}) {

  const cambiarPantalla = (nuevaPantalla) => {
    setPantalla(nuevaPantalla);

    if (window.innerWidth < 1024) {
      setSidebarAbierto(false);
    }
  };

  return (
    <>
      {/* FONDO OSCURO */}
      {sidebarAbierto && (
        <div
          className="
            lg:hidden
            fixed
            inset-0
            bg-black/50
            z-40
          "
          onClick={() => setSidebarAbierto(false)}
        />
      )}

      {/* SIDEBAR */}
      <aside
        className={`
          fixed lg:static
          top-0 left-0
          h-screen
          w-64
          bg-slate-900
          text-white
          flex
          flex-col
          shadow-lg
          z-50
          transform
          transition-transform
          duration-300

          ${
            sidebarAbierto
              ? "translate-x-0"
              : "-translate-x-full lg:translate-x-0"
          }
        `}
      >

        {/* LOGO */}
        <div className="p-6 border-b border-slate-800">

          <h1 className="text-3xl font-bold leading-tight">
            Sistema de
            <br />
            Salidas
          </h1>

        </div>

        {/* MENÚ */}
        <nav className="flex-1 p-4">

          <ul className="space-y-3">

            <li>

              <button
                onClick={() => cambiarPantalla("salidas")}
                className={`
                  w-full
                  px-4
                  py-3
                  rounded-xl
                  text-left
                  font-medium
                  transition
                  ${
                    pantalla === "salidas"
                      ? "bg-blue-600 shadow-md"
                      : "hover:bg-slate-800"
                  }
                `}
              >
                Registrar salida
              </button>

            </li>

            <li>

              <button
                onClick={() => cambiarPantalla("historial")}
                className={`
                  w-full
                  px-4
                  py-3
                  rounded-xl
                  text-left
                  font-medium
                  transition
                  ${
                    pantalla === "historial"
                      ? "bg-blue-600 shadow-md"
                      : "hover:bg-slate-800"
                  }
                `}
              >
                Historial
              </button>

            </li>

            <li>

              <button
                onClick={() => cambiarPantalla("reportes")}
                className={`
                  w-full
                  px-4
                  py-3
                  rounded-xl
                  text-left
                  font-medium
                  transition
                  ${
                    pantalla === "reportes"
                      ? "bg-blue-600 shadow-md"
                      : "hover:bg-slate-800"
                  }
                `}
              >
                Reportes
              </button>

            </li>

          </ul>

        </nav>

      </aside>
    </>
  );
}

export default Sidebar;