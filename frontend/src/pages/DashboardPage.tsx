import { useEffect, useState } from "react";
import { apiRequest } from "../api/client";
import { LanguageSwitcher } from "../components/LanguageSwitcher";
import type { Locale, Messages } from "../i18n/types";

type Option = {
  key: string;
  label?: string;
};

type Props = {
  locale: Locale;
  messages: Messages;
  onLocaleChange: (locale: Locale) => void;
  userName: string;
  token: string;
  onSelectOption: (optionKey: string) => void;
  onLogout: () => void;
};

export function DashboardPage({ locale, messages, onLocaleChange, userName, token, onSelectOption, onLogout }: Props) {
  const [options, setOptions] = useState<Option[]>([]);
  const [error, setError] = useState("");

  useEffect(() => {
    apiRequest<{ options: Option[] }>("/api/options", { token })
      .then((data) => setOptions(data.options))
      .catch((err) => setError(err instanceof Error ? err.message : messages.common.unknownError));
  }, [messages.common.unknownError, token]);

  return (
    <main className="container">
      <section className="card phone-frame">
        <div className="between">
          <div>
            <p className="eyebrow">{messages.dashboard.welcomeBack}</p>
            <h1>{userName}</h1>
          </div>
          <LanguageSwitcher locale={locale} label={messages.common.language} onChange={onLocaleChange} />
        </div>
        <div className="between">
          <p className="muted">{messages.dashboard.chooseNext}</p>
          <button className="ghost-btn" onClick={onLogout} type="button">
            {messages.common.logout}
          </button>
        </div>
        {error && <p className="error">{error}</p>}
        <div className="grid">
          {options.map((option) => (
            <button key={option.key} className="option-card" onClick={() => onSelectOption(option.key)} type="button">
              {messages.options[option.key] ?? option.label ?? option.key}
            </button>
          ))}
        </div>
      </section>
    </main>
  );
}
