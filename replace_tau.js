const fs = require('fs');
const path = require('path');

const dirsToScan = [
    'c:/laragon/www/TAU_CAMPUS_VIRTUAL/apps',
    'c:/laragon/www/TAU_CAMPUS_VIRTUAL/docker',
    'c:/laragon/www/TAU_CAMPUS_VIRTUAL/docs'
];

function scanAndReplace(dir) {
    if (!fs.existsSync(dir)) return;
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        const stat = fs.statSync(fullPath);
        if (stat.isDirectory()) {
            if (file === 'node_modules' || file === 'vendor' || file === '.git') continue;
            scanAndReplace(fullPath);
        } else {
            const ext = path.extname(file).toLowerCase();
            if (['.php', '.js', '.ts', '.tsx', '.css', '.scss', '.html', '.md', '.json', '.env'].includes(ext) || file === '.env.example' || file.includes('.php')) {
                let content = fs.readFileSync(fullPath, 'utf8');
                
                // We use regex to replace TAU Campus Virtual, but avoid cases where it already says E-TAU Campus Virtual
                // or <span class="e-tau-design">E</span>-TAU Campus Virtual
                const original = content;
                
                // Replace "TAU Campus Virtual" -> "E-TAU Campus Virtual" case-sensitive-ish
                content = content.replace(/(?<!(E-|>E<\/span>-))TAU Campus Virtual/g, 'E-TAU Campus Virtual');
                content = content.replace(/(?<!(E-|>E<\/span>-))TAU CAMPUS VIRTUAL/g, 'E-TAU CAMPUS VIRTUAL');
                
                if (content !== original) {
                    fs.writeFileSync(fullPath, content, 'utf8');
                    console.log('Updated:', fullPath);
                }
            }
        }
    }
}

dirsToScan.forEach(scanAndReplace);

// Also check root files
['c:/laragon/www/TAU_CAMPUS_VIRTUAL/.env.example'].forEach(f => {
    if (fs.existsSync(f)) {
        let content = fs.readFileSync(f, 'utf8');
        const original = content;
        content = content.replace(/(?<!(E-|>E<\/span>-))TAU Campus Virtual/g, 'E-TAU Campus Virtual');
        content = content.replace(/(?<!(E-|>E<\/span>-))TAU CAMPUS VIRTUAL/g, 'E-TAU CAMPUS VIRTUAL');
        if (content !== original) {
            fs.writeFileSync(f, content, 'utf8');
            console.log('Updated root:', f);
        }
    }
});
