import { en } from "./en";
import { nl } from "./nl";
import type { Locale, Messages } from "./types";

const dictionaries: Record<Locale, Messages> = { nl, en };

export function detectInitialLocale(): Locale {
  const saved = localStorage.getItem("locale");
  if (saved === "nl" || saved === "en") {
    return saved;
  }

  return navigator.language.toLowerCase().startsWith("nl") ? "nl" : "en";
}

export function getMessages(locale: Locale): Messages {
  return dictionaries[locale];
}
