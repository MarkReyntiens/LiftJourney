import { useState } from "react";
import { apiRequest } from "../api/client";
import { LanguageSwitcher } from "../components/LanguageSwitcher";
import type { Locale, Messages } from "../i18n/types";
import type { User } from "../types";

type Props = {
  locale: Locale;
  messages: Messages;
  onLocaleChange: (locale: Locale) => void;
  onAuthenticated: (token: string, user: User) => void;
};

type AuthResponse = {
  token: string;
  user: User;
};

export function AuthPage({ locale, messages, onLocaleChange, onAuthenticated }: Props) {
  const [mode, setMode] = useState<"login" | "register">("login");
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  const submit = async () => {
    try {
      setError("");
      const path = mode === "login" ? "/api/auth/login" : "/api/auth/register";
      const body = mode === "login" ? { email, password } : { name, email, password };
      const data = await apiRequest<AuthResponse>(path, { method: "POST", body });
      if (typeof data.token !== "string" || data.token.trim() === "") {
        throw new Error(messages.common.unknownError);
      }
      onAuthenticated(data.token, data.user);
    } catch (err) {
      setError(err instanceof Error ? err.message : messages.common.unknownError);
    }
  };

  return (
    <main className="container">
      <section className="card phone-frame">
        <div className="between">
          <div>
            <p className="eyebrow">{messages.auth.eyebrow}</p>
            <h1>{messages.auth.title}</h1>
          </div>
          <LanguageSwitcher locale={locale} label={messages.common.language} onChange={onLocaleChange} />
        </div>
        <p className="muted">{messages.auth.subtitle}</p>

        <div className="row">
          <button
            className={mode === "login" ? "segmented active" : "segmented"}
            onClick={() => setMode("login")}
            type="button"
          >
            {messages.auth.loginTab}
          </button>
          <button
            className={mode === "register" ? "segmented active" : "segmented"}
            onClick={() => setMode("register")}
            type="button"
          >
            {messages.auth.registerTab}
          </button>
        </div>

        {mode === "register" && (
          <label className="field">
            {messages.auth.name}
            <input value={name} onChange={(e) => setName(e.target.value)} />
          </label>
        )}

        <label className="field">
          {messages.auth.email}
          <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
        </label>

        <label className="field">
          {messages.auth.password}
          <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} />
        </label>

        {error && <p className="error">{error}</p>}
        <button className="btn-primary" onClick={submit} type="button">
          {mode === "login" ? messages.auth.loginButton : messages.auth.registerButton}
        </button>
      </section>
    </main>
  );
}
