UPGRADE From v2 to v3 of the money bundle
=========================================

We had to make some BC Breaks in order to migrate this bundle from
symfony 2 to symfony 3 because of changes to the form component of symfony.

We created a 2.x branch that remains compatible with symfony 2.x and the
master branch should work soon with symfony 3.x

Then we decided to integrate some small potential BC Breaks (hopefully
without any code modification of your projects.). The migration is listed here.
