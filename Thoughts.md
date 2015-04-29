# Introduction #

This is basically a  sounding board for the future of the product.  Things that may or may not end up actually being features.

# Thoughts #

## Module Installation ##

  * Have files available as .gz files (use gzdeflate to expand)
  * Run a cron job to run queued commands - works but is a horrible hack and maybe difficult for people to set up
  * Provide original files, download and put in place one at a time - problem if there is an upgrade
  * Find a way to untar a file after using gzdeflate to expand (then you can use supplied .tar.gz files)
  * Can currently add menu items, but maybe should also be able to add template pages
  * Every module needs to be able to provide configuration options which appear under the settings page when the module is installed.  If a module is removed, these settings should remain, so that if you install it again, you get it set up exactly how you had it
  * Will need to be able to check for the presence of any features that a package requires.  I.E. SmoothTorque needs jpgraph (as does Asterisk-Stat)

Would be nice to use tar.gz files inside the xml file.  Alas tar is not supported.

I could compress the files with gzip and use gzdeflate to bring them back but gzip doesn't support archiving (i.e. you can't compress a folder).

I really don't want people to have to resort to the command line - that's what's slowing it down a bit.

## Process ##

  * Provide option which allows you to specify what happens when you have finished with a record - i.e. go back to list\_customers.php or close the page

## Screen Popping ##

  * Could be done with VV AgentPopper or Flash Operator Panel, but I think it may be nicer if it was using an AJAX query to a backend DB.  I mean Comet would be heaps nicer but seems to fail on long term connections.
  * Different methods of agent login mean that different events need to be watched for in the Manager (i.e. AgentCallBackLogin or Dynamic Members vs Agent Login)

## Scripts and Forms ##

  * When talking to a customer there are many things that can be scripted.  Provide an interface to create these scripts and walk through them.