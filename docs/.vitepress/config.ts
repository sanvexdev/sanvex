import {defineConfig} from 'vitepress';

/**
 * Base path for assets and routes.
 * - GitHub Pages (project site): https://sanzgrapher.github.io/sanvex/ → '/sanvex/'
 * - Custom domain or Vercel at root: https://docs.example.com/ → '/'
 *
 * Set via env: VP_BASE=/sanvex/ npm run build
 * Local dev defaults to '/' (use VP_BASE=/sanvex/ npm run dev to match GitHub Pages).
 */
const base = process.env.VP_BASE || '/';

export default defineConfig({
  title: 'Sanvex',
  description: 'Laravel integrations for AI agents',
  base,
  lang: 'en-US',
  cleanUrls: true,
  head: [
    // Context7 widget — replace data-library with your library id from context7.com
    // Add allowed domains in Context7 dashboard (localhost + production URL).
    [
      'script',
      {
        src: 'https://context7.com/widget.js',
        'data-library': '/websites/sanvex_narayan-dhakal_np',
        'data-color': '#6366f1',
        'data-position': 'bottom-right',
        'data-placeholder': 'Ask about Sanvex…',
      },
    ],
  ],
  themeConfig: {
    logo: {
      src: '/logo.png',
      alt: 'Sanvex',
    },
    siteTitle: 'Sanvex',
    nav: [
      {text: 'Docs', link: '/'},
      {text: 'GitHub', link: 'https://github.com/sanzgrapher/sanvex'},
    ],
    sidebar: [
      {text: 'Welcome', link: '/'},
      {
        text: 'Getting Started',
        items: [
          {text: 'Quickstart', link: '/getting-started/quickstart'},
          {text: 'Installation', link: '/getting-started/installation'},
          {text: 'Usage', link: '/getting-started/usage'},
        ],
      },
      {text: 'Introduction', link: '/introduction'},
    ],
    socialLinks: [{icon: 'github', link: 'https://github.com/sanzgrapher/sanvex'}],
    search: {
      provider: 'local',
    },
    footer: {
      message: 'Released under the MIT License.',
      copyright: `Copyright © ${new Date().getFullYear()} Sanvex`,
    },
  },
});
