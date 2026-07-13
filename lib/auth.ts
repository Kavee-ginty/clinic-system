import { redirect } from "next/navigation";
import type { Role } from "@/lib/types";
import { supabaseAdmin, supabaseServer } from "@/lib/supabase/server";

export async function currentUserAndRoles() {
  const supabase = await supabaseServer();
  const { data } = await supabase.auth.getUser();
  const email = data.user?.email?.toLowerCase() ?? "";
  if (!data.user || !email) return { user: null, email: "", roles: [] as Role[] };

  const admin = supabaseAdmin();
  const { data: rows, error } = await admin.from("user_roles").select("role").eq("email", email);
  if (error) throw error;

  return {
    user: data.user,
    email,
    roles: (rows ?? []).map((row) => row.role as Role)
  };
}

export async function requireRole(allowed: Role[]) {
  const session = await currentUserAndRoles();
  if (!session.user) redirect("/");
  if (!session.roles.some((role) => allowed.includes(role))) redirect("/");
  return session;
}
