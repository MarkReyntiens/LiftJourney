import { useEffect, useMemo, useState } from "react";
import { detectInitialLocale, getMessages } from "./i18n";
import type { Locale } from "./i18n/types";
import { AuthPage } from "./pages/AuthPage";
import { DashboardPage } from "./pages/DashboardPage";
import { ExerciseCreatePage } from "./pages/ExerciseCreatePage";
import type { User } from "./types";

type Screen = "dashboard" | "exercise-create";

export function App() {
  const [token, setToken] = useState<string>(() => localStorage.getItem("token") ?? "");
  const [user, setUser] = useState<User | null>(null);
  const [screen, setScreen] = useState<Screen>("dashboard");
  const [locale, setLocale] = useState<Locale>(() => detectInitialLocale());

  const authenticated = useMemo(() => token !== "", [token]);
  const messages = useMemo(() => getMessages(locale), [locale]);

  useEffect(() => {
    localStorage.setItem("locale", locale);
  }, [locale]);

  if (!authenticated) {
    return (
      <AuthPage
        locale={locale}
        messages={messages}
        onLocaleChange={setLocale}
        onAuthenticated={(newToken, currentUser) => {
          localStorage.setItem("token", newToken);
          setToken(newToken);
          setUser(currentUser);
        }}
      />
    );
  }

  if (screen === "exercise-create") {
    return (
      <ExerciseCreatePage
        locale={locale}
        messages={messages}
        onLocaleChange={setLocale}
        token={token}
        onBack={() => setScreen("dashboard")}
      />
    );
  }

  return (
    <DashboardPage
      locale={locale}
      messages={messages}
      onLocaleChange={setLocale}
      userName={user?.name ?? messages.dashboard.fallbackUserName}
      onLogout={() => {
        localStorage.removeItem("token");
        setToken("");
        setUser(null);
      }}
      onSelectOption={(optionKey) => {
        if (optionKey === "create-exercise") {
          setScreen("exercise-create");
        }
      }}
      token={token}
    />
  );
}
