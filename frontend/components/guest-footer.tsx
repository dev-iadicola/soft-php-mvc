type FooterLink = {
  href: string;
  label: string;
};

export type FooterSection = {
  links: FooterLink[];
  title: string;
};

type GuestFooterProps = {
  appName: string;
  sections: FooterSection[];
};

export function GuestFooter({ appName, sections }: GuestFooterProps) {
  return (
    <footer className="guest-footer">
      <div className="guest-footer__lead">
        <p className="guest-footer__eyebrow">Guest shell</p>
        <h2 className="guest-footer__title">{appName}</h2>
        <p className="guest-footer__copy">
          Layout condiviso per pagine marketing, blog, portfolio e flussi guest
          come login, sign-up e reset password.
        </p>
      </div>

      <div className="guest-footer__grid">
        {sections.map((section) => (
          <section key={section.title} className="guest-footer__section">
            <h3 className="guest-footer__section-title">{section.title}</h3>
            <ul className="guest-footer__links">
              {section.links.map((link) => (
                <li key={link.href}>
                  <a className="guest-footer__link" href={link.href}>
                    {link.label}
                  </a>
                </li>
              ))}
            </ul>
          </section>
        ))}
      </div>
    </footer>
  );
}
