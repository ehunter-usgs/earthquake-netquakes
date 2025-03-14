/* global L */
'use strict';

var Util = require('util/Util');

/**
 * Factory for Ocean base layer
 *
 * @param options {Object}
 *     L.TileLayer options
 *
 * @return {L.TileLayer}
 */
var OceanLayer = function (options) {
  options = Util.extend({
    maxZoom: 16,
  }, options);

  return L.tileLayer(
    'https://services.arcgisonline.com/ArcGIS/rest/services/Ocean/World_Ocean_Base/MapServer/tile/{z}/{y}/{x}',
    options
  );
};

L.oceanLayer = OceanLayer;

module.exports = OceanLayer;
