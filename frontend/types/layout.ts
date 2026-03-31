import type { BreadcrumbItem } from '@/components/guest-breadcrumb';
import type { FooterSection } from '@/components/guest-footer';
import type { NavigationItem } from '@/components/guest-header';

export type GuestLayoutContent = {
  breadcrumbs?: BreadcrumbItem[];
  footerSections?: FooterSection[];
  navigation?: NavigationItem[];
};
