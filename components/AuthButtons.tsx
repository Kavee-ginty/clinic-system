"use client";

import { supabaseBrowser } from "@/lib/supabase/client";

export function GoogleSignIn() {
  async function signIn() {
    const supabase = supabaseBrowser();
    await supabase.auth.signInWithOAuth({
      provider: "google",
      options: { redirectTo: `${location.origin}/auth/callback` }
    });
  }

  return (
    <button onClick={signIn} className="w-full rounded-lg bg-teal-600 p-3 font-bold text-white shadow-md transition hover:bg-teal-700">
      Continue with Google
    </button>
  );
}

export function SignOutButton() {
  async function signOut() {
    const supabase = supabaseBrowser();
    await supabase.auth.signOut();
    location.href = "/";
  }

  return (
    <button onClick={signOut} className="rounded bg-red-600 px-3 py-1 text-sm font-bold text-white">
      Logout
    </button>
  );
}
