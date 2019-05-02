var fixtures = fixtures || {};

fixtures.data = {
  simple: [
    { value: 'big' },
    { value: 'bigger' },
    { value: 'biggest' },
    { value: 'small' },
    { value: 'smaller' },
    { value: 'smallest' }
  ],
  animals: [
    { value: 'dog' },
    { value: 'cat' },
    { value: 'moose' }
  ]
};

fixtures.serialized = {
  simple: {
    "datums": {
        "{\"value\":\"big\"}": {
            "value": "big"
        },
        "{\"value\":\"bigger\"}": {
            "value": "bigger"
        },
        "{\"value\":\"biggest\"}": {
            "value": "biggest"
        },
        "{\"value\":\"small\"}": {
            "value": "small"
        },
        "{\"value\":\"smaller\"}": {
            "value": "smaller"
        },
        "{\"value\":\"smallest\"}": {
            "value": "smallest"
        }
    },
    "trie": {
        "i": [],
        "c": {
            "b": {
                "i": ["{\"value\":\"big\"}", "{\"value\":\"bigger\"}", "{\"value\":\"biggest\"}"],
                "c": {
                    "i": {
                        "i": ["{\"value\":\"big\"}", "{\"value\":\"bigger\"}", "{\"value\":\"biggest\"}"],
                        "c": {
                            "g": {
                                "i": ["{\"value\":\"big\"}", "{\"value\":\"bigger\"}", "{\"value\":\"biggest\"}"],
                                "c": {
                                    "g": {
                                        "i": ["{\"value\":\"bigger\"}", "{\"value\":\"biggest\"}"],
                                        "c": {
                                            "e": {
                                                "i": ["{\"value\":\"bigger\"}", "{\"value\":\"biggest\"}"],
                                                "c": {
                                                    "r": {
                                                        "i": ["{\"value\":\"bigger\"}"],
                                                        "c": {}
                                                    },
                                                    "s": {
                                                        "i": ["{\"value\":\"biggest\"}"],
                                                        "c": {
                                                            "t": {
                                                                "i": ["{\"value\":\"biggest\"}"],
                                                                "c": {}
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            },
            "s": {
                "i": ["{\"value\":\"small\"}", "{\"value\":\"smaller\"}", "{\"value\":\"smallest\"}"],
                "c": {
                    "m": {
                        "i": ["{\"value\":\"small\"}", "{\"value\":\"smaller\"}", "{\"value\":\"smallest\"}"],
                        "c": {
                            "a": {
                                "i": ["{\"value\":\"small\"}", "{\"value\":\"smaller\"}", "{\"value\":\"smallest\"}"],
                                "c": {
                                    "l": {
                                        "i": ["{\"value\":\"small\"}", "{\"value\":\"smaller\"}", "{\"value\":\"smallest\"}"],
                                        "c": {
                                            "l": {
                                                "i": ["{\"value\":\"small\"}", "{\"value\":\"smaller\"}", "{\"value\":\"smallest\"}"],
                                                "c": {
                                                    "e": {
                                                        "i": ["{\"value\":\"smaller\"}", "{\"value\":\"smallest\"}"],
                                                        "c": {
                                                            "r": {
                                                                "i": ["{\"value\":\"smaller\"}"],
                                                                "c": {}
                                                            },
                                                            "s": {
                                                                "i": ["{\"value\":\"smallest\"}"],
                                                                "c": {
                                                                    "t": {
                                                                        "i": ["{\"value\":\"smallest\"}"],
                                                                        "c": {}
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
}
