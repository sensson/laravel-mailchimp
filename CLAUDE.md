## Code Style

- Happy path comes last: use early returns for guard clauses, never end a method in an `if` statement
- All code must be fully typed: methods, properties, parameters, return types, and closures — no untyped variables
- Prefer single-word variable names over multi-word (e.g. `$audience` not `$audienceList`)
- If a `__construct()` method has no body, use `//` as the body
- DX is critical: follow Laravel's fluent style for package APIs

## Commits

- Use semantic commit messages (e.g. `feat:`, `fix:`, `chore:`, `refactor:`)
- Commit messages must be fully lowercase
- First line must not exceed 50 characters
- Only include a body if it helps build understanding of the issue — usually omit it
- Never add Co-Authored-By lines

## Testing

- Tests are vital and must accompany all changes
- Write user-facing tests: for packages, the user is the developer consuming the package
- Test the public API and behavior, not internals

## Quality

- Always run Pint: `./vendor/bin/pint`
- Always run PHPStan at level 9: `./vendor/bin/phpstan analyse --level=9`
