import {defineConfig} from 'vitepress';

export default defineConfig({
  title: 'Sanvex',
  description: 'Laravel integrations for AI agents',
  lang: 'en-US',
  cleanUrls: true,
  rewrites: {
    'getting-started/:page': 'docs/getting-started/:page',
    'introduction': 'docs/introduction',
  },
  head: [
    [
      'script',
      {
        src: 'https://context7.com/widget.js',
        defer: 'defer',
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
      {text: 'Docs', link: '/docs/getting-started/quickstart'},
      {text: 'GitHub', link: 'https://github.com/sanvexdev/sanvex'},
    ],
    sidebar: [
      {
        text: 'Getting Started',
        items: [
          {text: 'Quickstart', link: '/docs/getting-started/quickstart'},
          {text: 'Installation', link: '/docs/getting-started/installation'},
          {text: 'Usage', link: '/docs/getting-started/usage'},
        ],
      },
      {text: 'Introduction', link: '/docs/introduction'},
    ],
    socialLinks: [{icon: 'github', link: 'https://github.com/sanvexdev/sanvex'}],
    search: {
      provider: 'local',
    },
    footer: {
      message: 'Released under the MIT License.',
      copyright: `Copyright © ${new Date().getFullYear()} Sanvex`,
    },
  },
});
