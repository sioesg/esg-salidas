import { useState } from "react";
import logo from "../assets/images/logo.png";
import { login } from "../services/authApi";

function Login({ onLogin }) {
  const [usuario, setUsuario] = useState("");
  const [password, setPassword] = useState("");
  const [cargando, setCargando] = useState(false);
  const [error, setError] = useState("");

  const iniciarSesion = async (e) => {
    e.preventDefault();
    setError("");

    if (
      usuario.trim() === "" ||
      password.trim() === ""
    ) {
      setError("Completa todos los campos");
      return;
    }

    setCargando(true);

    try {
      const response = await login({
        email: usuario.trim(),
        password,
      });

      onLogin(response.user);
    } catch (error) {
      if (error.status === 401 || error.status === 422) {
        setError("Usuario o contrasena incorrectos");
      } else {
        setError("No fue posible iniciar sesion");
      }
    } finally {
      setCargando(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100 px-4">

      <div className="bg-white p-10 rounded-2xl shadow-lg w-full max-w-md">

        {/* LOGO */}
        <div className="flex justify-center mb-6">
          <img
            src={logo}
            alt="Logo"
            className="h-24 object-contain"
          />
        </div>

        {/* TITULO */}
        <h1 className="text-3xl font-bold text-center text-gray-800">
          Sistema de Salidas
        </h1>


        {/* FORMULARIO */}
        <form
          onSubmit={iniciarSesion}
          className="space-y-5"
        >

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
            </label>

            <input
              type="email"
              placeholder="Ingresa tu correo"
              value={usuario}
              onChange={(e) => setUsuario(e.target.value)}
              className="w-full h-12 border border-gray-300 rounded-lg px-4
              focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
            </label>

            <input
              type="password"
              placeholder="Ingresa tu contrasena"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full h-12 border border-gray-300 rounded-lg px-4
              focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          {error && (
            <p className="text-sm text-red-600">
              {error}
            </p>
          )}

          <button
            type="submit"
            disabled={cargando}
            className="w-full h-12 bg-blue-600 hover:bg-blue-700
            text-white font-semibold rounded-lg transition"
          >
            {cargando ? "Ingresando..." : "Ingresar"}
          </button>

        </form>

      </div>

    </div>
  );
}

export default Login;
