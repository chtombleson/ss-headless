# SS Headless

A set of extensions to make using silverstripe as a headless cms easier.

There are currently two extensions:

  * PageController: This has a couple of hand functions for outputting into templates etc.
  * StaticJson: This is used to create static json files of pages and other data objects that can be either written to disk or uploaded to s3.

## Docs

### PageController Extension

By default this is added to the PageController class. It adds two functions:

  * StaticJSONUrl(), return an absolute link to the static json file.
  * JSON(), return the json data for an object.

### StaticJSON Extension

By default this is added to the SiteTree class. It has a bunch of hooks that are used to create the static json file or remove them as needed.

Default Config:

    Chtombleson\SSHeadless\Extension\StaticJSON:
      default_json_fields:
        - column: ID
          key: id
        - column: GUID
          key: guid
        - column: ClassName
          key: class_name
        - column: Created
          key: created_at
        - column: LastEdited
          key: updated_at

You can also set the default fields that are added to the json data.

