import { useState } from "react";
import logo from "../assets/images/logo.png";

function Login({ onLogin }) {
  const [usuario, setUsuario] = useState("");
  const [password, setPassword] = useState("");

  const iniciarSesion = (e) => {
    e.preventDefault();

    if (
      usuario.trim() !== "" &&
      password.trim() !== ""
    ) {
      onLogin();
    } else {
      alert("Completa todos los campos");
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
              type="text"
              placeholder="Ingresa tu usuario"
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
              placeholder="Ingresa tu contraseña"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full h-12 border border-gray-300 rounded-lg px-4
              focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <button
            type="submit"
            className="w-full h-12 bg-blue-600 hover:bg-blue-700
            text-white font-semibold rounded-lg transition"
          >
            Ingresar
          </button>

        </form>

      </div>

    </div>
  );
}

export default Login;