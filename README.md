EDD Stream
==========
Display all audio or video files from an EDD download.

## Usage
* Current download's videos, only show to those who have purchased.
`[edd_stream]`
* Current download's videos, show to anyone:
`[edd_stream restrict="false"]`
* Some other download's videos:
`[edd_stream id="42"]`
* Current download's audio files, only show to those who have purchased.
`[edd_stream type="audio"]`
  * NOTE: Audio does not work yet.
* Show download's videos, only for those who have purchased, with a login form & message for non-logged in users
`[edd_stream show_login="true" login_message="If you have purchased this course, login to view the files"]`
  
## Copyright/ License, ETC.
Copyright 2016 Josh Pollock. Licensed under the terms of the GPL v2 or later.
