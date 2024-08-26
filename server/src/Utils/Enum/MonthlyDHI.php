<?php

namespace App\Controller;

class MonthlyDHI
{
    public static $monthlyDHI = [
      "inputs" => [
            "location" => [
               "latitude" => 45.609,
               "longitude" => 16.288,
               "elevation" => 95
            ],
            "meteo_data" => [
                  "radiation_db" => "PVGIS-SARAH2",
                  "meteo_db" => "ERA5",
                  "year_min" => 2020,
                  "year_max" => 2020,
                  "use_horizon" => true,
                  "horizon_db" => null,
                  "horizon_data" => "DEM-calculated"
               ],
            "plane" => [
                     "fixed_horizontal" => [
                        "slope" => [
                           "value" => 0,
                           "optimal" => "-"
                        ],
                        "azimuth" => [
                              "value" => "-",
                              "optimal" => "-"
                           ]
                     ],
                     "fixed_inclined" => [
                                 "slope" => [
                                    "value" => 34,
                                    "optimal" => false
                                 ],
                                 "azimuth" => [
                                       "value" => 0,
                                       "optimal" => false
                                    ]
                              ],
                     "fixed_inclined_optimal" => [
                                          "slope" => [
                                             "value" => 39,
                                             "optimal" => true
                                          ],
                                          "azimuth" => [
                                                "value" => 0,
                                                "optimal" => false
                                             ]
                                       ]
                  ]
         ],
      "outputs" => [
                                                   "monthly" => [
                                                      [
                                                         "year" => 2020,
                                                         "month" => 1,
                                                         "H(h)_m" => 48.14,
                                                         "H(i_opt)_m" => 94.27,
                                                         "H(i)_m" => 90.33,
                                                         "Hb(n)_m" => 88.88
                                                      ],
                                                      [
                                                            "year" => 2020,
                                                            "month" => 2,
                                                            "H(h)_m" => 66.94,
                                                            "H(i_opt)_m" => 109.04,
                                                            "H(i)_m" => 105.87,
                                                            "Hb(n)_m" => 98.87
                                                         ],
                                                      [
                                                               "year" => 2020,
                                                               "month" => 3,
                                                               "H(h)_m" => 111.4,
                                                               "H(i_opt)_m" => 148.45,
                                                               "H(i)_m" => 146.57,
                                                               "Hb(n)_m" => 128.53
                                                            ],
                                                      [
                                                                  "year" => 2020,
                                                                  "month" => 4,
                                                                  "H(h)_m" => 182.11,
                                                                  "H(i_opt)_m" => 211.19,
                                                                  "H(i)_m" => 211.56,
                                                                  "Hb(n)_m" => 206.59
                                                               ],
                                                      [
                                                                     "year" => 2020,
                                                                     "month" => 5,
                                                                     "H(h)_m" => 169.23,
                                                                     "H(i_opt)_m" => 167.42,
                                                                     "H(i)_m" => 170.25,
                                                                     "Hb(n)_m" => 140.7
                                                                  ],
                                                      [
                                                                        "year" => 2020,
                                                                        "month" => 6,
                                                                        "H(h)_m" => 182.56,
                                                                        "H(i_opt)_m" => 172.89,
                                                                        "H(i)_m" => 176.79,
                                                                        "Hb(n)_m" => 159.59
                                                                     ],
                                                      [
                                                                           "year" => 2020,
                                                                           "month" => 7,
                                                                           "H(h)_m" => 207.91,
                                                                           "H(i_opt)_m" => 201.47,
                                                                           "H(i)_m" => 205.84,
                                                                           "Hb(n)_m" => 199.32
                                                                        ],
                                                      [
                                                                              "year" => 2020,
                                                                              "month" => 8,
                                                                              "H(h)_m" => 168.09,
                                                                              "H(i_opt)_m" => 181.09,
                                                                              "H(i)_m" => 182.67,
                                                                              "Hb(n)_m" => 160.94
                                                                           ],
                                                      [
                                                                                 "year" => 2020,
                                                                                 "month" => 9,
                                                                                 "H(h)_m" => 135.04,
                                                                                 "H(i_opt)_m" => 167.29,
                                                                                 "H(i)_m" => 166.33,
                                                                                 "Hb(n)_m" => 141.72
                                                                              ],
                                                      [
                                                                                    "year" => 2020,
                                                                                    "month" => 10,
                                                                                    "H(h)_m" => 76.56,
                                                                                    "H(i_opt)_m" => 109.94,
                                                                                    "H(i)_m" => 107.76,
                                                                                    "Hb(n)_m" => 88.35
                                                                                 ],
                                                      [
                                                                                       "year" => 2020,
                                                                                       "month" => 11,
                                                                                       "H(h)_m" => 33.1,
                                                                                       "H(i_opt)_m" => 50.33,
                                                                                       "H(i)_m" => 49.03,
                                                                                       "Hb(n)_m" => 34.98
                                                                                    ],
                                                      [
                                                                                          "year" => 2020,
                                                                                          "month" => 12,
                                                                                          "H(h)_m" => 23.83,
                                                                                          "H(i_opt)_m" => 34.77,
                                                                                          "H(i)_m" => 33.95,
                                                                                          "Hb(n)_m" => 21.28
                                                                                       ]
                                                   ]
                                                ],
      "meta" => [
                                                                                             "inputs" => [
                                                                                                "location" => [
                                                                                                   "description" => "Selected location",
                                                                                                   "variables" => [
                                                                                                      "latitude" => [
                                                                                                         "description" => "Latitude",
                                                                                                         "units" => "decimal degree"
                                                                                                      ],
                                                                                                      "longitude" => [
                                                                                                            "description" => "Longitude",
                                                                                                            "units" => "decimal degree"
                                                                                                         ],
                                                                                                      "elevation" => [
                                                                                                               "description" => "Elevation",
                                                                                                               "units" => "m"
                                                                                                            ]
                                                                                                   ]
                                                                                                ],
                                                                                                "meteo_data" => [
                                                                                                                  "description" => "Sources of meteorological data",
                                                                                                                  "variables" => [
                                                                                                                     "radiation_db" => [
                                                                                                                        "description" => "Solar radiation database"
                                                                                                                     ],
                                                                                                                     "meteo_db" => [
                                                                                                                           "description" => "Database used for meteorological variables other than solar radiation"
                                                                                                                        ],
                                                                                                                     "year_min" => [
                                                                                                                              "description" => "First year of the calculations"
                                                                                                                           ],
                                                                                                                     "year_max" => [
                                                                                                                                 "description" => "Last year of the calculations"
                                                                                                                              ],
                                                                                                                     "use_horizon" => [
                                                                                                                                    "description" => "Include horizon shadows"
                                                                                                                                 ],
                                                                                                                     "horizon_db" => [
                                                                                                                                       "description" => "Source of horizon data"
                                                                                                                                    ]
                                                                                                                  ]
                                                                                                               ],
                                                                                                "plane" => [
                                                                                                                                          "description" => "plane",
                                                                                                                                          "fields" => [
                                                                                                                                             "slope" => [
                                                                                                                                                "description" => "Inclination angle from the horizontal plane",
                                                                                                                                                "units" => "degree"
                                                                                                                                             ],
                                                                                                                                             "azimuth" => [
                                                                                                                                                   "description" => "Orientation (azimuth) angle of the (fixed) PV system (0 = S, 90 = W, -90 = E)",
                                                                                                                                                   "units" => "degree"
                                                                                                                                                ]
                                                                                                                                          ]
                                                                                                                                       ]
                                                                                             ],
                                                                                             "outputs" => [
                                                                                                                                                      "monthly" => [
                                                                                                                                                         "type" => "time series",
                                                                                                                                                         "timestamp" => "monthly averages",
                                                                                                                                                         "variables" => [
                                                                                                                                                            "H(h)_m" => [
                                                                                                                                                               "description" => "Irradiation on horizontal plane",
                                                                                                                                                               "units" => "kWh/m2/mo"
                                                                                                                                                            ],
                                                                                                                                                            "H(i_opt)_m" => [
                                                                                                                                                                  "description" => "Irradiation on optimally inclined plane",
                                                                                                                                                                  "units" => "kWh/m2/mo"
                                                                                                                                                               ],
                                                                                                                                                            "H(i)_m" => [
                                                                                                                                                                     "description" => "Irradiation on plane at angle",
                                                                                                                                                                     "units" => "kWh/m2/mo"
                                                                                                                                                                  ],
                                                                                                                                                            "Hb(n)_m" => [
                                                                                                                                                                        "description" => "Monthly beam (direct) irradiation on a plane always normal to sun rays",
                                                                                                                                                                        "units" => "kWh/m2/mo"
                                                                                                                                                                     ]
                                                                                                                                                         ]
                                                                                                                                                      ]
                                                                                                                                                   ]
                                                                                          ]
   ];
}
