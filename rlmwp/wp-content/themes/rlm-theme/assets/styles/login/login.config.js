import { commonConfig, assetsFolder } from '../common/common.config'

module.exports = {
   content: [
      `${assetsFolder}/modules/login/*.php`,
      `${assetsFolder}/modules/login/**/*.php`,
      `${assetsFolder}/assets/scripts/login/*.js`,
      `${assetsFolder}/assets/scripts/login/**/*.js`,
   ],
   theme: commonConfig.theme,
   plugins: commonConfig.plugins,
   safelist: commonConfig.safelist,
   darkMode: commonConfig.darkMode,
}
