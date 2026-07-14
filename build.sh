#!/usr/bin/env bash
set -euo pipefail

MODULE_NAME="iiromanesti_ai_seo"
VERSION="0.1.0"
ARCHIVE_NAME="${MODULE_NAME}-v${VERSION}.zip"
DIST_DIR="dist"
BUILD_DIR="${DIST_DIR}/build"
PACKAGE_DIR="${BUILD_DIR}/${MODULE_NAME}"
ARCHIVE_PATH="${DIST_DIR}/${ARCHIVE_NAME}"

rm -rf "${BUILD_DIR}"
mkdir -p "${PACKAGE_DIR}" "${DIST_DIR}"

cp README.md index.php iiromanesti_ai_seo.php "${PACKAGE_DIR}/"
mkdir -p "${PACKAGE_DIR}/classes"
cp classes/ApiClient.php classes/Logger.php classes/index.php "${PACKAGE_DIR}/classes/"

rm -f "${ARCHIVE_PATH}"

if command -v zip >/dev/null 2>&1; then
    (
        cd "${BUILD_DIR}"
        zip -qr "../${ARCHIVE_NAME}" "${MODULE_NAME}"
    )
else
    python3 - <<'PY'
from pathlib import Path
from zipfile import ZipFile, ZIP_DEFLATED

module_name = 'iiromanesti_ai_seo'
dist_dir = Path('dist')
build_dir = dist_dir / 'build'
archive_path = dist_dir / f'{module_name}-v0.1.0.zip'
with ZipFile(archive_path, 'w', ZIP_DEFLATED) as archive:
    for path in sorted((build_dir / module_name).rglob('*')):
        if path.is_file():
            archive.write(path, path.relative_to(build_dir))
PY
fi

rm -rf "${BUILD_DIR}"
printf 'Created %s\n' "${ARCHIVE_PATH}"
