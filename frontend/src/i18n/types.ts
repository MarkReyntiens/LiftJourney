export type Locale = "nl" | "en";

export type Messages = {
  common: {
    language: string;
    back: string;
    logout: string;
    unknownError: string;
  };
  auth: {
    eyebrow: string;
    title: string;
    subtitle: string;
    loginTab: string;
    registerTab: string;
    name: string;
    email: string;
    password: string;
    loginButton: string;
    registerButton: string;
  };
  dashboard: {
    welcomeBack: string;
    chooseNext: string;
    fallbackUserName: string;
  };
  exercise: {
    eyebrow: string;
    title: string;
    name: string;
    description: string;
    imageUrl: string;
    setsCount: string;
    startReps: string;
    hasWeight: string;
    startWeight: string;
    muscleGroups: string;
    saveButton: string;
    savedSuccess: string;
    saveError: string;
  };
  options: Record<string, string>;
};
