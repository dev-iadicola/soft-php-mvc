type PublicSectionHeaderProps = {
  eyebrow: string;
  title: string;
  description?: string;
};

export function PublicSectionHeader({
  eyebrow,
  title,
  description,
}: PublicSectionHeaderProps) {
  return (
    <div className="space-y-2">
      <p className="text-xs font-semibold tracking-[0.16em] text-brand-700 uppercase">
        {eyebrow}
      </p>
      <div className="space-y-2">
        <h2 className="text-2xl font-semibold text-slate-950">{title}</h2>
        {description ? <p className="max-w-3xl text-sm text-slate-600">{description}</p> : null}
      </div>
    </div>
  );
}
