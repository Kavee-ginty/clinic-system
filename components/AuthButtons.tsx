"use client";

import { supabaseBrowser } from "@/lib/supabase/client";

export function GoogleSignIn({ label = "Continue with Google", className = "w-full rounded-lg bg-teal-600 p-3 font-bold text-white shadow-md transition hover:bg-teal-700", next = "/" }: { label?: string; className?: string; next?: string }) {
  async function signIn() {
    const supabase = supabaseBrowser();
    await supabase.auth.signInWithOAuth({
      provider: "google",
      options: { redirectTo: `${location.origin}/auth/callback?next=${encodeURIComponent(next)}` }
    });
  }

  return (
    <button onClick={signIn} className={className}>
      {label}
    </button>
  );
}

export function SignOutButton({ className = "rounded bg-red-600 px-3 py-1 text-sm font-bold text-white", label = "Logout" }: { className?: string; label?: string }) {
  async function signOut() {
    const supabase = supabaseBrowser();
    await supabase.auth.signOut();
    location.href = "/";
  }

  return <button onClick={signOut} className={className}>{label}</button>;
}
