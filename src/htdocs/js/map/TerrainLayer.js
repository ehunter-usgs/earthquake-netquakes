/* global L */
'use strict';


var Util = require('hazdev-webutils/src/util/Util');


/**
 * Factory for ESRI Terrain base layer
 *
 * @param options {Object}
 *     L.TileLayer options
 *
 * @return {L.TileLayer}
 */
var TerrainLayer = function (options) {
  options = Util.extend({
    maxZoom: 16,
  }, options);

  return L.tileLayer(
    'https://basemap.nationalmap.gov/arcgis/rest/services/USGSTopo/MapServer/tile/{z}/{y}/{x}',
    options
  );
};


L.terrainLayer = TerrainLayer;

module.exports = TerrainLayer;
