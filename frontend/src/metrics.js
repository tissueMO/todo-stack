const fs = require('fs');
const acorn = require('acorn');
const walk = require('acorn-walk');

if (process.argv.length !== 4) {
  console.log('node vuejs_metrics srcdir outputfile.csv');
  process.exit(1);
}

const srcdir = process.argv[2];
const outpath = process.argv[3];

const funcType = [
  'ArrowFunctionExpression',
  'FunctionDeclaration',
  'FunctionExpression'
];
const incType = [
  'IfStatement',
  'SwitchStatement',
  'ForStatement',
  'ForOfStatement',
  'ForInStatement',
  'DoWhileStatement',
  'WhileStatement',
  'CatchClause',
  'LogicalExpression',
  'ConditionalExpression'
];
const excludeDir = [
  'dist',
  'node_modules'
];

class Parser {
  constructor (file, src) {
    this.file = file;
    this.src = src;
    console.log('Parse', file);
  }

  analyzeFuncNode (node, path, functionList) {
    // 関数のnode
    let funcName = '';
    if (path.length >= 2) {
      const parentNode = path[path.length - 2];
      if (node.type === 'ArrowFunctionExpression' ||
        node.type === 'FunctionDeclaration') {
        if (node.id && node.id.name) {
          funcName = node.id.name;
        }
      } else if (node.type === 'FunctionExpression') {
        if (parentNode.type === 'Property' || parentNode.type === 'MethodDefinition') {
          funcName = parentNode.key.name;
        }
      }
    }
    const func = functionList.find((f) => {
      return f.node.start === node.start && f.node.end === node.end;
    });
    if (!func) {
      functionList.push(
        {
          node,
          name: funcName,
          complex: 1,
          code: this.src.substr(node.start, node.end - node.start).replace(/\r?\n/g, '').replace(/\t?\n/g, '').slice(0, 200)
        }
      );
    }
  }

  analyze () {
    const functionList = [];
    const ast = acorn.parse(this.src, { sourceType: 'module', ecmaVersion: 2020 });
    functionList.push({
      node: {
        start: 0,
        end: this.src.length - 1
      },
      name: 'root',
      complex: 1,
      code: 'ファイル参照'
    });
    walk.fullAncestor(ast, (node, path) => {
      if (funcType.includes(node.type)) {
        this.analyzeFuncNode(node, path, functionList);
      } else if (node.type === 'CallExpression') {
        // 関数の引数で関数が指定された場合、fullAncestorで上がってこない。
        const funcs = node.arguments.filter((n) => {
          return funcType.includes(n.type);
        });
        if (funcs.length > 0) {
          this.analyzeFuncNode(node, path, functionList);
        }
      }
    });
    walk.fullAncestor(ast, (node, path) => {
      if (!incType.includes(node.type)) {
        return;
      }
      let pathFuncNodes = path.filter((p) => {
        if (funcType.includes(p.type)) {
          return true;
        }
        if (p.type === 'CallExpression') {
          const funcs = p.arguments.filter((n) => {
            return funcType.includes(n.type);
          });
          if (funcs) {
            return true;
          }
        }
        return false;
      });
      if (pathFuncNodes.length === 0) {
        pathFuncNodes = [functionList[0].node];
      }
      const func = functionList.find((f) => {
        return pathFuncNodes.find((pf) => {
          return f.node.start === pf.start && f.node.end === pf.end;
        });
      });
      if (!func) {
        console.log('notfound--------------', this.file, pathFuncNodes);
        console.log(node, this.src.substr(node.start, node.end - node.start));
        // functionList.map((p)=>{console.log(p)});
        // path.map((p)=>{console.log(p, this.src.substr(p.start, p.end-p.start))});
        return;
      }
      if (node.type === 'SwitchStatement') {
        func.complex += node.cases.length;
      } else {
        func.complex += 1;
      }
    });
    const result = [];
    functionList.forEach((f) => {
      result.push(
        `${this.file}\t${f.name}\t${f.complex}\t${f.code}`
      );
    });
    return result;
  }
}

const enumlateFiles = dir =>
  fs.readdirSync(dir, { withFileTypes: true }).flatMap((dirent) => {
    if (dirent.isDirectory()) {
      if (excludeDir.includes(dirent.name)) {
        console.log(dirent.name);
        return [];
      }
      return enumlateFiles(`${dir}/${dirent.name}`);
    } else {
      return [`${dir}/${dirent.name}`];
    }
  });

const files = enumlateFiles(srcdir);
let result = [];

files.forEach((file) => {
  if (file.endsWith('.js')) {
    const src = fs.readFileSync(file, 'utf-8');
    const parser = new Parser(file, src);
    result = result.concat(parser.analyze());
  } else if (file.endsWith('.vue')) {
    const doc = fs.readFileSync(file, 'utf-8');
    const srcs = doc.match(/(?<=<script>)[\s\S]*?(?=<\/script>)/g);
    if (!srcs) {
      return;
    }
    srcs.forEach((src) => {
      const parser = new Parser(file, src);
      result = result.concat(parser.analyze());
    });
  }
});
fs.writeFileSync(outpath, result.join('\n'));
