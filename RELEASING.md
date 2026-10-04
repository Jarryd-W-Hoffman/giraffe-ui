# Releasing

GiraffeUI follows [Semantic Versioning](https://semver.org). Before 1.0, minor versions (0.x.0) may include breaking changes.

## First release only

1. Make the GitHub repository public.
2. Turn on **Private vulnerability reporting**: Settings → Code security. `SECURITY.md` points there.
3. Submit `https://github.com/Jarryd-W-Hoffman/GiraffeUI` at [packagist.org/packages/submit](https://packagist.org/packages/submit).
4. Connect Packagist to GitHub so new tags publish on their own: [packagist.org/about#how-to-update-packages](https://packagist.org/about#how-to-update-packages).

## Every release

1. In `CHANGELOG.md`, change `## [x.y.z] - Unreleased` to today's date, e.g. `## [0.1.0] - 2026-10-20`, and add a fresh empty `## [Unreleased]` section above it.
2. Commit, then tag and push:

   ```bash
   git tag v0.1.0
   git push origin v0.1.0
   ```

3. The **Release** workflow creates a GitHub release with that version's changelog section as its notes (marked as a pre-release for 0.x). It fails if the section is missing or still says Unreleased.
