import { useState } from 'react';

import { BrandMark } from '@/components/brand-mark';
import { cn } from '@/lib/cn';

export type NavigationItem = {
  href: string;
  label: string;
};

type HeaderAction = {
  href: string;
  label: string;
  variant?: 'ghost' | 'primary';
};

type GuestHeaderProps = {
  currentPath: string;
  navigation: NavigationItem[];
  primaryAction?: HeaderAction;
  secondaryAction?: HeaderAction;
};

export function GuestHeader({
  currentPath,
  navigation,
  primaryAction,
  secondaryAction,
}: GuestHeaderProps) {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const closeMenu = () => setIsMobileMenuOpen(false);

  return (
    <header className="guest-header">
      <div className="guest-header__bar">
        <a className="guest-header__brand" href="/" onClick={closeMenu}>
          <BrandMark compact />
        </a>

        <nav className="guest-header__nav" aria-label="Primary navigation">
          {navigation.map((item) => (
            <a
              key={item.href}
              className={cn('guest-header__nav-link', {
                'guest-header__nav-link--active': currentPath === item.href,
              })}
              href={item.href}
            >
              {item.label}
            </a>
          ))}
        </nav>

        <div className="guest-header__actions">
          {secondaryAction ? (
            <a
              className="guest-header__action guest-header__action--ghost"
              href={secondaryAction.href}
            >
              {secondaryAction.label}
            </a>
          ) : null}

          {primaryAction ? (
            <a
              className="guest-header__action guest-header__action--primary"
              href={primaryAction.href}
            >
              {primaryAction.label}
            </a>
          ) : null}

          <button
            type="button"
            className="guest-header__menu-button"
            aria-expanded={isMobileMenuOpen}
            aria-label="Toggle navigation"
            onClick={() => setIsMobileMenuOpen((value) => !value)}
          >
            <span />
            <span />
            <span />
          </button>
        </div>
      </div>

      <div
        className={cn('guest-mobile-nav', {
          'guest-mobile-nav--open': isMobileMenuOpen,
        })}
      >
        <div className="guest-mobile-nav__panel">
          <div className="guest-mobile-nav__header">
            <BrandMark compact />
            <button
              type="button"
              className="guest-mobile-nav__close"
              aria-label="Close navigation"
              onClick={closeMenu}
            >
              Close
            </button>
          </div>

          <nav className="guest-mobile-nav__links" aria-label="Mobile navigation">
            {navigation.map((item) => (
              <a
                key={item.href}
                className={cn('guest-mobile-nav__link', {
                  'guest-mobile-nav__link--active': currentPath === item.href,
                })}
                href={item.href}
                onClick={closeMenu}
              >
                {item.label}
              </a>
            ))}
          </nav>

          <div className="guest-mobile-nav__footer">
            {secondaryAction ? (
              <a
                className="guest-header__action guest-header__action--ghost"
                href={secondaryAction.href}
                onClick={closeMenu}
              >
                {secondaryAction.label}
              </a>
            ) : null}

            {primaryAction ? (
              <a
                className="guest-header__action guest-header__action--primary"
                href={primaryAction.href}
                onClick={closeMenu}
              >
                {primaryAction.label}
              </a>
            ) : null}
          </div>
        </div>
      </div>
    </header>
  );
}
