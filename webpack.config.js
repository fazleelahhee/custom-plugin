const webpack = require('webpack');
const path = require('path');

function getPlugin() {
    if (process.env.NODE_ENV === 'production') {
        return [
            new webpack.optimize.UglifyJsPlugin()
        ];
    } else {
        return [];
    }
}

config = {
    entry: {
        main: ['./assets/ts/app.ts']
    },
    output: {
        filename: 'wpcplugin.js',
        path: path.resolve(__dirname, 'public/js')
    },
    resolve: {
        // Add '.ts' and '.tsx' as a resolvable extension.
        extensions: ['.ts', '.tsx', '.js'],
        alias: {}
    },
    plugins: [],
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                use: 'ts-loader'
            }
        ]
    }
};

module.exports = config;