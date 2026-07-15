import { useEffect, useState } from "react";

import Login from "./pages/Login";

import Sidebar from "./components/Sidebar";
import Header from "./components/Header";
import ErrorBoundary from "./components/ErrorBoundary";

import RegistrarSalida from "./pages/RegistrarSalida";
import Historial from "./pages/Historial";
import Reportes from "./pages/Reportes";

import { logout, me } from "./services/authApi";

function App() {

  const [usuario, setUsuario] = useState(null);

  const [verificandoSesion, setVerificandoSesion] = useState(true);

  const [pantalla, setPantalla] = useState("salidas");

  const [sidebarAbierto, setSidebarAbierto] = useState(false);

  useEffect(() => {
    const restaurarSesion = async () => {
      try {
        const usuarioAutenticado = await me();
        setUsuario(usuarioAutenticado);
      } catch {
        setUsuario(null);
      } finally {
        setVerificandoSesion(false);
      }
    };

    restaurarSesion();
  }, []);

  const iniciarSesion = (usuarioAutenticado) => {
    setUsuario(usuarioAutenticado);
  };

  const cerrarSesion = async () => {
    try {
      await logout();
    } finally {
      setUsuario(null);
      setPantalla("salidas");
      setSidebarAbierto(false);
    }
  };

  if (verificandoSesion) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-100">
        <p className="text-gray-600">
          Verificando sesion...
        </p>
      </div>
    );
  }

  // LOGIN
  if (!usuario) {
    return (
      <Login
        onLogin={iniciarSesion}
      />
    );
  }

  return (
    <ErrorBoundary>
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
          usuario={usuario}
        />

        {/* PAGINAS */}
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
    </ErrorBoundary>

  );
}

export default App;
