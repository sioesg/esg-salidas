const SESSION_KEY = "esg_temp_session";

const TEMPORARY_USERS = [
  {
    email: "sistemas@esg.com.mx",
    password: "password",
    user: {
      email: "sistemas@esg.com.mx",
      name: "Sistemas",
      role: { nombre: "Temporal" },
    },
  },
  {
    email: "dgalvan@esg.com.mx",
    password: "password",
    user: {
      email: "dgalvan@esg.com.mx",
      name: "D. Galvan",
      role: { nombre: "David Galván" },
    },
  },
];

// TEMPORAL / NO PRODUCCION:
// Login local de demostracion. No consulta Laravel, Sanctum ni MySQL.
// No se persiste password; solo datos minimos de sesion en sessionStorage.
export const login = async ({ email, password }) => {
  const normalizedEmail = email.trim().toLowerCase();
  const usuarioTemporal = TEMPORARY_USERS.find(
    (item) => item.email === normalizedEmail && item.password === password
  );

  if (!usuarioTemporal) {
    const error = new Error("Usuario o contrasena incorrectos");
    error.status = 401;
    throw error;
  }

  sessionStorage.setItem(SESSION_KEY, JSON.stringify(usuarioTemporal.user));

  return { user: usuarioTemporal.user };
};

export const me = async () => {
  const session = sessionStorage.getItem(SESSION_KEY);

  if (!session) {
    const error = new Error("Sesion temporal no encontrada");
    error.status = 401;
    throw error;
  }

  return JSON.parse(session);
};

export const logout = async () => {
  sessionStorage.removeItem(SESSION_KEY);
  return null;
};

export const authApi = {
  login,
  me,
  logout,
};
