# survos/dummy-bundle

Doctrine-backed demo entities and a loader for DummyJSON data.

Current scope:
- `Product` and `Image` entities
- repository services
- Doctrine mapping registration by the bundle
- `dummy:load` command

This bundle is currently about loading demo data. It does not provide a UI.

## Quickstart

Copy and paste this into a shell:

```bash
symfony new dummy-demo --webapp
cd dummy-demo
echo 'DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db' > .env.local
composer req survos/dummy-bundle
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:schema:update --force
bin/console dummy:load --purge
bin/console doctrine:query:sql "select count(*) from product"
```

## Local Path Repository

If you are developing this bundle locally from `~/sites/mono/bu/dummy-bundle`, initialize the app like this instead:

```bash
symfony new dummy-demo --webapp
cd dummy-demo
echo 'DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db' > .env.local
composer config repositories.survos-dummy path ~/sites/mono/bu/dummy-bundle
composer req survos/dummy-bundle
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:schema:update --force
bin/console dummy:load --purge
bin/console doctrine:query:sql "select count(*) from product"
```

## Notes

- The bundle currently loads products from `https://dummyjson.com/products?limit=200` by default.
- Use `bin/console dummy:load --source=/path/to/products.json` to load from a local file.
- Use `bin/console dummy:load --limit=20` to import a smaller sample.
- Use `bin/console dummy:load --purge` to clear `image` and `product` rows before reloading.
