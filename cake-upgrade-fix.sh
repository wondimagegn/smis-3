#!/bin/bash

# Usage:
# ./cake-upgrade-fix.sh src/Controller [--dry]

TARGET_DIR=${1:-"src/Controller"}
DRYRUN=false
LOG_FILE="migration.log"

if [[ "$2" == "--dry" ]]; then
  DRYRUN=true
  echo "🧪 Running in DRY-RUN mode (no files will be modified)..."
 
else
  echo "🔁 Applying replacements in: $TARGET_DIR"
  echo "📝 Logging modified files to: $LOG_FILE"
  echo "" > $LOG_FILE
fi

# Files to skip
EXCLUDE_FILES=(
  "AppController.php"
  "UsersController.php"
  "DashboardController.php"
  "Component/AcademicYearComponent.php"
  "Component/AttachmentComponent.php"
  "Component/AttemptComponent.php"
  "Component/BrowserComponent.php"
  "Component/CustomAclComponent.php"
  "Component/DataTableComponent.php"
  "Component/EthiopicDateTimeComponent.php"
  "Component/HighchartsComponent.php"
  "Component/MailComponent.php"
  "Component/MathCaptchaComponent.php"
  "Component/MenuOptimizedComponent.php"
  "Component/TimezoneComponent.php"
)

# SED replacements
SED_COMMANDS=(
  "s/ClassRegistry::init(/TableRegistry::getTableLocator()->get(/g"
  "s/\\\$this->Session/\\\$this->getRequest()->getSession()/g"
  "s/\\\$this->request->data/\\\$this->getRequest()->getData()/g"
  "s/\\\$this->request->query/\\\$this->getRequest()->getQuery()/g"
  "s/\\\$this->request->is(/\\\$this->getRequest()->is(/g"
  "s/\\\$this->params/\\\$this->getRequest()->getParam()/g"
)

# Discover files
FILES=$(find "$TARGET_DIR" -type f -name "*.php" | while read -r file; do
  skip=false
  for exclude in "${EXCLUDE_FILES[@]}"; do
    if [[ "$file" == *"$exclude" ]]; then
      skip=true
      break
    fi
  done
  if ! $skip; then
    echo "$file"
  fi
done)

# Apply replacements
for file in $FILES; do
  changes=0
  for pattern in "${SED_COMMANDS[@]}"; do
    if grep -q "$(echo "$pattern" | sed 's/.*s\/\(.*\)\/.*/\1/')" "$file"; then
      if $DRYRUN; then
        echo "🟡 [$file] would apply: $pattern"
      else
        sed -i "$pattern" "$file"
        changes=1
      fi
    fi
  done
  
  
  if [[ "$changes" -eq 1 ]]; then
	  if $DRYRUN; then
	    echo "🟡 DRYRUN: $file"
	    echo "$file" >> "$LOG_FILE"
	  else
	    echo "✅ Modified: $file"
	    echo "$file" >> "$LOG_FILE"
	  fi
   fi

done

echo "🎉 Done: $([[ $DRYRUN == true ]] && echo 'DRY run' || echo 'Live run, see migration.log')"
