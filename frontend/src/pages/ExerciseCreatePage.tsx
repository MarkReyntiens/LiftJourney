import { useEffect, useState } from "react";
import { apiRequest } from "../api/client";
import { LanguageSwitcher } from "../components/LanguageSwitcher";
import type { Locale, Messages } from "../i18n/types";
import type { MuscleGroup } from "../types";

type Props = {
  locale: Locale;
  messages: Messages;
  onLocaleChange: (locale: Locale) => void;
  token: string;
  onBack: () => void;
};

export function ExerciseCreatePage({ locale, messages, onLocaleChange, token, onBack }: Props) {
  const [muscleGroups, setMuscleGroups] = useState<MuscleGroup[]>([]);
  const [name, setName] = useState("");
  const [description, setDescription] = useState("");
  const [imageUrl, setImageUrl] = useState("");
  const [setsCount, setSetsCount] = useState(3);
  const [startReps, setStartReps] = useState(10);
  const [hasWeight, setHasWeight] = useState(true);
  const [startWeightKg, setStartWeightKg] = useState("20");
  const [selectedMuscles, setSelectedMuscles] = useState<number[]>([]);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");

  useEffect(() => {
    apiRequest<{ muscleGroups: MuscleGroup[] }>("/api/muscle-groups", { token })
      .then((data) => setMuscleGroups(data.muscleGroups))
      .catch((err) => setError(err instanceof Error ? err.message : messages.common.unknownError));
  }, [messages.common.unknownError, token]);

  const toggleMuscle = (id: number) => {
    setSelectedMuscles((prev) => (prev.includes(id) ? prev.filter((item) => item !== id) : [...prev, id]));
  };

  const submit = async () => {
    try {
      setError("");
      setMessage("");
      await apiRequest<{ id: number }>("/api/exercises", {
        method: "POST",
        token,
        body: {
          name,
          description,
          imageUrl,
          muscleGroupIds: selectedMuscles,
          setsCount,
          startReps,
          startWeightKg: hasWeight ? Number(startWeightKg) : null,
        },
      });
      setMessage(messages.exercise.savedSuccess);
    } catch (err) {
      setError(err instanceof Error ? err.message : messages.exercise.saveError);
    }
  };

  return (
    <main className="container">
      <section className="card phone-frame">
        <div className="between">
          <div>
            <p className="eyebrow">{messages.exercise.eyebrow}</p>
            <h1>{messages.exercise.title}</h1>
          </div>
          <LanguageSwitcher locale={locale} label={messages.common.language} onChange={onLocaleChange} />
        </div>
        <button className="ghost-btn" onClick={onBack} type="button">
          {messages.common.back}
        </button>

        <label className="field">
          {messages.exercise.name}
          <input value={name} onChange={(e) => setName(e.target.value)} />
        </label>

        <label className="field">
          {messages.exercise.description}
          <textarea value={description} onChange={(e) => setDescription(e.target.value)} />
        </label>

        <label className="field">
          {messages.exercise.imageUrl}
          <input value={imageUrl} onChange={(e) => setImageUrl(e.target.value)} />
        </label>

        <label className="field">
          {messages.exercise.setsCount}
          <input type="number" value={setsCount} min={1} onChange={(e) => setSetsCount(Number(e.target.value))} />
        </label>

        <label className="field">
          {messages.exercise.startReps}
          <input type="number" value={startReps} min={1} onChange={(e) => setStartReps(Number(e.target.value))} />
        </label>

        <label className="checkbox">
          <input type="checkbox" checked={hasWeight} onChange={(e) => setHasWeight(e.target.checked)} />
          {messages.exercise.hasWeight}
        </label>

        {hasWeight && (
          <label className="field">
            {messages.exercise.startWeight}
            <input
              type="number"
              min={0}
              step={0.5}
              value={startWeightKg}
              onChange={(e) => setStartWeightKg(e.target.value)}
            />
          </label>
        )}

        <p className="muted">{messages.exercise.muscleGroups}</p>
        <div className="chips">
          {muscleGroups.map((group) => (
            <button
              key={group.id}
              type="button"
              className={selectedMuscles.includes(group.id) ? "chip chip-active" : "chip"}
              onClick={() => toggleMuscle(group.id)}
            >
              {group.name}
            </button>
          ))}
        </div>

        {error && <p className="error">{error}</p>}
        {message && <p className="success">{message}</p>}
        <button className="btn-primary" onClick={submit} type="button">
          {messages.exercise.saveButton}
        </button>
      </section>
    </main>
  );
}
