import type { Locale } from "../i18n/types";

type Props = {
  locale: Locale;
  label: string;
  onChange: (locale: Locale) => void;
};

export function LanguageSwitcher({ locale, label, onChange }: Props) {
  return (
    <div className="lang-switcher" aria-label={label}>
      <button
        type="button"
        className={locale === "nl" ? "lang-btn active" : "lang-btn"}
        onClick={() => onChange("nl")}
      >
        NL
      </button>
      <button
        type="button"
        className={locale === "en" ? "lang-btn active" : "lang-btn"}
        onClick={() => onChange("en")}
      >
        EN
      </button>
    </div>
  );
}
