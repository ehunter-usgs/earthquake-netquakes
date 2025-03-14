/* global L, MOUNT_PATH */
'use strict';


var Xhr = require('hazdev-webutils/src/util/Xhr');

// Factories for creating map layers (returns e.g. "L.earthquakesLayer()")
require('map/OceanLayer');
require('map/RequestedLayer');
require('map/TerrainLayer');


/*
 * Factory for Leaflet map instance on sign-up page
 */
var SignupMap = function (options) {
  var _this,
      _initialize,

      _el,
      _requested,

      _getMapLayers,
      _initMap,
      _loadRequestedLayer;


  _this = {};


  _initialize = function (options) {
    options = options || {};
    _el = options.el || document.createElement('div');

    // Load requested layer which calls initMap() when finished
    _loadRequestedLayer();
  };

  /**
   * Get all map layers that will be displayed on map
   *
   * @return layers {Object}
   *     {
   *       baseLayers: {Object}
   *       overlays: {Object}
   *       defaults: {Array}
   *     }
   */
  _getMapLayers = function () {
    var ocean,
        layers,
        terrain;

    terrain = L.terrainLayer();
    ocean = L.oceanLayer();

    layers = {};
    layers.baseLayers = {
      'Terrain': terrain,
      'Ocean': ocean,
    };
    layers.overlays = {
      'Requested sites': _requested,
    };
    layers.defaults = [terrain, _requested];

    return layers;
  };

  /**
   * Create Leaflet map instance
   */
  _initMap = function () {
    var bounds,
        layers,
        map;

    layers = _getMapLayers();

    // Create map
    map = L.map(_el, {
      attributionControl: false,
      layers: layers.defaults,
      scrollWheelZoom: false,
    });

    // Set intial map extent to contain requested sites overlay
    bounds = _requested.getBounds();
    map.fitBounds(bounds);

    // Add controllers
    L.control.scale().addTo(map);
    L.control.layers(layers.baseLayers, layers.overlays).addTo(map);
  };

  /**
   * Load requested sites layer from geojson data via ajax
   */
  _loadRequestedLayer = function () {
    Xhr.ajax({
      url: MOUNT_PATH + '/_getRequested.json.php',
      success: function (data) {
        _requested = L.requestedLayer({
          data: data
        });
        _initMap();
      },
      error: function (status) {
        console.log(status);
      }
    });
  };

  _initialize(options);
  options = null;
  return _this;
};


module.exports = SignupMap;
