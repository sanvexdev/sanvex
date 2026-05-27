import {defineConfig} from 'vitepress';

export default defineConfig({
  title: 'Sanvex',
  description: 'Laravel integrations for AI agents',
  lang: 'en-US',
  cleanUrls: true,
  rewrites: {
    'getting-started/:page': 'docs/getting-started/:page',
    'introduction': 'docs/introduction',
    'concepts/:page': 'docs/concepts/:page',
    'integrations/:page': 'docs/integrations/:page',
    'drivers/:page': 'docs/drivers/:page',
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
      link: '/docs/getting-started/quickstart',
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
      {
        text: 'Concepts',
        items: [
          {text: 'Introduction', link: '/docs/introduction'},
          {text: 'Packages', link: '/docs/concepts/packages'},
          {text: 'Database', link: '/docs/concepts/database'},
          {text: 'Authentication', link: '/docs/concepts/authentication'},
          {text: 'Tenancy', link: '/docs/concepts/tenancy'},
        ],
      },
      {
        text: 'Integrations',
        items: [
          {text: 'Laravel AI', link: '/docs/integrations/laravel-ai'},
          {text: 'MCP', link: '/docs/integrations/mcp'},
        ],
      },
      {
        text: 'Drivers',
        items: [
          {text: 'Overview', link: '/docs/drivers/'},
          {text: 'GitHub', link: '/docs/drivers/github'},
          {text: 'Gmail', link: '/docs/drivers/gmail'},
          {text: 'Linear', link: '/docs/drivers/linear'},
          {text: 'Notion', link: '/docs/drivers/notion'},
          {text: 'Slack', link: '/docs/drivers/slack'},
        ],
      },
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
