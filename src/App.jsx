import { useState } from "react";

import Login from "./pages/Login";

import Sidebar from "./components/Sidebar";
import Header from "./components/Header";

import RegistrarSalida from "./pages/RegistrarSalida";
import Historial from "./pages/Historial";
import Reportes from "./pages/Reportes";

function App() {

  const [logueado, setLogueado] = useState(false);

  const [pantalla, setPantalla] = useState("salidas");

  const [sidebarAbierto, setSidebarAbierto] = useState(false);

  const iniciarSesion = () => {
    setLogueado(true);
  };

  const cerrarSesion = () => {
    setLogueado(false);
    setPantalla("salidas");
    setSidebarAbierto(false);
  };

  // LOGIN
  if (!logueado) {
    return (
      <Login
        onLogin={iniciarSesion}
      />
    );
  }

  return (

    <div className="flex h-screen bg-gray-100 overflow-hidden">

      {/* SIDEBAR */}
      <Sidebar
        pantalla={pantalla}
        setPantalla={setPantalla}
        sidebarAbierto={sidebarAbierto}
        setSidebarAbierto={setSidebarAbierto}
      />

      {/* CONTENIDO */}
      <div className="flex-1 min-w-0 flex flex-col">

        {/* HEADER */}
        <Header
          pantalla={pantalla}
          setSidebarAbierto={setSidebarAbierto}
          onLogout={cerrarSesion}
        />

        {/* PÁGINAS */}
        <main
          className="
            flex-1
            overflow-auto
            p-4
            md:p-6
            lg:p-8
          "
        >

          {pantalla === "salidas" && (
            <RegistrarSalida />
          )}

          {pantalla === "historial" && (
            <Historial />
          )}

          {pantalla === "reportes" && (
            <Reportes />
          )}

        </main>

      </div>

    </div>

  );
}

export default App;