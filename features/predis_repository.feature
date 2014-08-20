Feature: Repository persistence
  Objects should be stored in redis with the appropriate "index" keys

  Scenario: Saving an object with "indexed" properties
    Given the following Car:
      | id | make  | model   | engine_type | color |
      | 1  | Tesla | Model S | V8          | red   |
    Then there should be 6 keys in the database
    And the following keys should exist:
      | name           | value |
      | make:Tesla     | 1     |
      | model:Model S  | 1     |
      | engine_type:V8 | 1     |
      | color:red      | 1     |

  Scenario: Finding an object by it's id
    Given the following Car:
      | id | make  | model   | engine_type | color |
      | 1  | Tesla | Model S | V8          | red   |
    And the car with id "1" has the property "attributes" with the following values:
      | is_favorite | yes |
      | is_slow     | no  |
      | is_awd      | yes |
    And the car with id "1" has the property "owners" with the following values:
      | 0 | one |
      | 1 | two |
    When I find a Car by id 1
    Then there should be 1 car
    And the car with the id 1 should have the following properties:
      | id | make  | model   | engine_type | color |
      | 1  | Tesla | Model S | V8          | red   |
    And the car with the id 1 should have property "attributes" with the following values:
      | is_favorite | yes |
      | is_slow     | no  |
      | is_awd      | yes |

  Scenario: Saving an object with null property values removes the id from index field for that property
    Given the following Car:
      | id | make    | model   | engine_type | color  |
      | 1  | Tesla   | Model S | V8          | red    |
      | 2  | Porsche | 911     | V8          | yellow |
    Then there should be 2 items in the "manufacture_date" key
    When I set the manufacture date to null
    Then there should be 1 items in the "manufacture_date" key
    And When I set the color for the car "1" to "null"
    Then there should be 0 items in the "color:red" key

