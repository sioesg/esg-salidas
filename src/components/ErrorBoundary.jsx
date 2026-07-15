import { Component } from "react";

class ErrorBoundary extends Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false };
  }

  static getDerivedStateFromError() {
    return { hasError: true };
  }

  componentDidCatch(error, info) {
    console.error("Error de render en la aplicacion", error, info);
  }

  render() {
    if (this.state.hasError) {
      return (
        <div className="min-h-screen flex items-center justify-center bg-gray-100 p-6">
          <div className="bg-white rounded-2xl shadow-sm p-8 max-w-md text-center">
            <h1 className="text-2xl font-bold text-gray-800">
              Ocurrio un error
            </h1>
            <p className="mt-3 text-gray-600">
              No fue posible mostrar esta pantalla. Cierra y vuelve a abrir la aplicacion.
            </p>
          </div>
        </div>
      );
    }

    return this.props.children;
  }
}

export default ErrorBoundary;
