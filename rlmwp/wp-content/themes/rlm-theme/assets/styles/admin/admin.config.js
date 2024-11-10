import { commonConfig, assetsFolder } from '../common/common.config'

module.exports = {
   content: [
      `${assetsFolder}/includes/*.php`,
      `${assetsFolder}/includes/**/*.php`,
      `${assetsFolder}/assets/scripts/admin/*.js`,
      `${assetsFolder}/assets/scripts/admin/**/*.js`,
   ],
   theme: commonConfig.theme,
   plugins: commonConfig.plugins,
}
