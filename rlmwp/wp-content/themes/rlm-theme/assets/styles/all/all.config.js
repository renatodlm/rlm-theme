import { commonConfig, assetsFolder } from '../common/common.config'

module.exports = {
   content: [
      `${assetsFolder}/*.php`,
      `${assetsFolder}/**/*.php`,
      `${assetsFolder}/components/*.php`,
      `${assetsFolder}/components/**/*.php`,
      `${assetsFolder}/assets/scripts/all/*.js`,
      `${assetsFolder}/assets/scripts/all/**/*.js`,
   ],
   theme: commonConfig.theme,
   plugins: commonConfig.plugins,
   safelist: commonConfig.safelist,
   darkMode: commonConfig.darkMode,
}
