## What is DamnSmallBlog?
DamnSmallBlog is a barebones and lightweight self hosted blog engine.

It takes seconds to get up and running on any server that supports PHP

## Why?
*Aren't there a billion blog engines out there already? Why write a new one?*

I had been meaning to set up somewhere for opinion pieces and linking assorted projects 
for sometime, but was never happy with the other engines that always came with too much cruft
in the backend or the templates available just seemed over designed and delivered too much beyond
the actual content.

There has been a trend in delivering frameworks at several hundred k or more just to display text on a screen.

I wanted something that was about the content, not the look. As long the front end _does not get in the way_ 
and the backend is accessible from *whatever* internet connected device I happen to have available then this works for me.

As a side note, with more of the world coming online in areas with slow and expensive connections, I think 
there is a potential demand for fast and lightweight publishing platforms...

## Features
* Very lightweight
* Articles written in Markdown
* Built in sharing for Facebook, Reddit, Twitter, HackerNews
* Automatic caching of files for low end servers.
* No Javascript at all delivered to end user (unless you enable Google Analytics or include a video) 
* Javascript only used in the CMS for enabling Full screen writing mode.
* Embededyoutube  video support in Markdown
* Lighting fast (visitors are being served HTML files direct)
* Full screen distraction free writing mode
* Works on ALL browsers, including text only such as Lynx
* Super Mobile friendly
* Image uploader included

## Requirements
A server with PHP5+ that also has write access to the file system for creating folders and publishing files.

## Installation
Upload everything to the root folder (no support for subfolder installation yet) and visit the homepage in a browser.


## Usage
Everything should be obvious although I will write better instructions here in due course

Posts are written using [Markdown](https://daringfireball.net/projects/markdown/syntax)

Youtube videos can be embedded with 

^ {video-id}

e.g.

^ wCDIYvFmgW8

## Demo
No demo of the CMS at the moment - but my site [Malcx](http://www.malcx.com) is the product of the front end.

## Credits
This uses a, currently adapted, version of [Parsedown](https://github.com/erusev/parsedown)

Much of the front end layout and styling is taken from: [bettermotherfuckingwebsite.com](http://bettermotherfuckingwebsite.com/)

## Current planned upgrades:
* Auto generated sitemap.xml
* Add preview option for posts
* Auto generate robots.txt
* Upload and include favicon
* Amend ParseDown to handle youtube correctly as a plugin
* Restructure CMS interface to allow 
