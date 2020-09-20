# Installation

- `git clone git@gitlab.com:farpat/shop-symfony.git`
- `cp .env.example .env` 
- Change .env to correspond with your environment
- `make install`
- And enjoy!
- (To display help run `make help` or `make`)

# Use

`make dev` : start a development servers that allow the automic refresh.
(Don't forget to stop development servers : `make stop-dev` when you finished)

`make build` : build assets to deploy project on production. 

`make update` : Update all versions of packages in package.json and update the composer packages.
