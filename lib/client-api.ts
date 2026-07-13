"use client";

export async function apiGet<T>(action: string, query?: Record<string, string | number | undefined>) {
  const params = new URLSearchParams();
  Object.entries(query ?? {}).forEach(([key, value]) => value !== undefined && params.set(key, String(value)));
  const res = await fetch(`/api/clinic/${action}${params.size ? `?${params}` : ""}`);
  const data = await res.json();
  if (!res.ok || data?.success === false) throw new Error(data?.error || "Request failed");
  return data as T;
}

export async function apiPost<T>(action: string, body: unknown) {
  const res = await fetch(`/api/clinic/${action}`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body)
  });
  const data = await res.json();
  if (!res.ok || data?.success === false) throw new Error(data?.error || "Request failed");
  return data as T;
}
