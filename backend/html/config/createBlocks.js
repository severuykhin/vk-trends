"use strict";
const { params, plugins: { fs } } = require("./variables");

let folder, file, fileCss, fileJs, textCss, textJs;

const getBreakpoint = (lvl, direction = "up") => `@include media-breakpoint-${direction}(${lvl})`;

const createLessFromJade = function (file, blockName, screen) {
    let textLess = `${getBreakpoint(screen)} {\n  .${blockName} {\n`;

    let selectors = [];

    let replacer = function (str, p1) {
        if(selectors.indexOf(p1) === -1) {
            selectors.push(p1);
            textLess += `    &${p1.replace(`.${blockName}`, "")} {\n\n    }\n`;
        }
    };
    file.replace(new RegExp(`(\\.${blockName}__[\\s\\S]+?)(?=\\s|!|=|\\(|\\.|:)`, "gi"), replacer);
    return `${textLess}  }\n}${(screen === "xs" ? `\n\n${getBreakpoint("xs", "down")} {\n\n}` : "")}`;
};

module.exports = function () {
    let contentFileJade;
    for ( let blockName of params.blocksName ) {
        contentFileJade = fs.readFileSync(`./blocks/${blockName}/${blockName}.pug`, "utf8");

        for ( let level of params.levels ) {
            textCss  = createLessFromJade(contentFileJade, blockName, level);

            folder   = `./blocks/${blockName}/${level}`; // ./blocks/example/xs.example
            file     = `${folder}/${blockName}`;
            fileCss  = `${file}.scss`;
            fileJs   = `${file}.js`;
            textJs   = `export default function init${blockName.charAt(0).toUpperCase() + blockName.slice(1)}() \n{ \n //Module code goes here \n}`;

            try {
                fs.mkdirSync(folder);
                fs.writeFileSync(fileCss, textCss);

                if(level === "xs") {
                    fs.writeFileSync(fileJs, textJs);
                }
            } catch (err) {
                console.log(`${blockName}-${folder} already exists!`);
            }

        }
    }
}