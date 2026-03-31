type BrandMarkProps = {
  compact?: boolean;
};

export function BrandMark({ compact = false }: BrandMarkProps) {
  return (
    <div className="brand-mark">
      <span className="brand-mark__eyebrow">Soft MVC</span>
      {!compact ? <span className="brand-mark__title">React bootstrap</span> : null}
    </div>
  );
}
