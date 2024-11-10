export const assetsFolder = './rlmwp/wp-content/themes/rlm-theme'

const fallbackFonts = [
   'ui-sans-serif',
   'system-ui',
   '-apple-system',
   'BlinkMacSystemFont',
   'Segoe UI',
   'Roboto',
   'Helvetica Neue',
   'Arial',
   'Noto Sans',
   'sans-serif',
   'Apple Color Emoji',
   'Segoe UI Emoji',
   'Segoe UI Symbol',
   'Noto Color Emoji',
]

export const commonConfig = {
   theme: {
      container: {
         center: true,
         padding: {
            DEFAULT: '15px',
         },
      },
      extend: {
         fontFamily: {
            primary: ['Inter', ...fallbackFonts],
            secondary: ['Inter', ...fallbackFonts],
         },
         colors: {
            gray: {
            },
            neutral: {
            },
         },
         aspectRatio: {
         },
      },
   },
   plugins: [],
   safelist: [
      'hidden'
   ],
}
