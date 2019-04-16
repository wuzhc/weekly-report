## 周报生成器
> 获取每个项目的git仓库提交日志,进行去重,过滤导出到文件或直接输出到控制台

## 安装
```bash
# 拉取代码
git pull https://github.com/wuzhc/weekly-report.git

# 进入工作目录
cd weekly-report

# 安装
composer install -vvv
```

## 配置
复制一份`.env.example`到`.env`,修改`.env`配置文件
```bash
# git提交用户
AUTHOR=wuzhc

# 从第几天查找,周报一般是前5天的数据(周五到周一)
SINCE_DAY=5

# 仓库目录,多个仓库目录逗号隔开
REPOSITORIES=/data/wwwroot/php/food

# 报告人名字
USERNAME=吴小弟
```

## 执行
```bash
php index.php -m=excel|text|console
```
说明:共支持3种模式
- excel 导出到excel,默认以`weekly-report/src/output/template/default.xlsx`为模板文件
- text 导出到txt文本文件
- console 直接输出到控制台