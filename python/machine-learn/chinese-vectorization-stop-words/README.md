如何用Python和机器学习训练中文文本情感分类模型

虚拟环境的配置文件  environment.yaml

首先执行以下命令：

conda env create -f environment.yaml

所需的软件包就一次性安装完毕，之后执行

source activate datapy3

进入这个虚拟环境

注意一定要执行下面这句：



python -m ipykernel install --user --name=datapy3



只有这样，当前的Python环境才会作为核心（kernel）在系统中注册



详细解释：https://www.jianshu.com/p/29aa3ad63f9d