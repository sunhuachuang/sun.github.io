#对于redux的几个例子的分析
##首先是redux的流程分析
redux包括三个概念：action，reducer，store
action: 如同指令。形式：{type, data}，要操作的方式（新建，更新，删除）与数据（id, data）
reducer: 核心：（old store, action） => new store 。处理action发送过来的指令。
store: 全局唯一，一切数据的来源地。

##第一个counter
初始化执行流程：index.html -> index.js -> action -> render -> createStore -> app.js(containers) -> components
交互流程：components->action->reder->store->container->component

###目录结构
####主要的

d-actions: 存放函数化好的action()
d-components: react组建存放的地方，相当与view
d-containers: 构造最顶层模块，连接react及stroe传入变成props
d-reducers: 存放reduer
d-store: 需要加入middleware的store，构造特殊的store
index.html: 初始化页面
index.js: 进入js(react-redux的标准构造)
webpack.config.js: webpack打包配置 输入输出配置

####次要的
server.js: 后台server
package.json: node包管理
d-node_modules: npm包
d-test

###render单纯的处理store与action
如果数据需要和服务器交互的化，action里面的data在传到render之前便处理好
处理的过程就在middleware。
render处理增删改查与动作逻辑都是单纯的。都是类似的。

redux 中的 combineReducers 用于将reducer模块化，分割开。名称相同。

dispatch一般是一个可用于回调的函数。即dispatch(action)

####containers中:
react-redux 中的connent方法就是将state与dispatch方法通过mapStateToProps与mapDispatchToProps当成props传到react中

redux 中的 bindActionCreators 方法，将action自动转化为dispatch回调函数。名称相同。
异步执行的dispatch,调用的时候用回调函数的方式。


store是一个数据整体。全局唯一一个数据来源。可以在其中以数组形式存入不同数据。json形式的数据.

action也是json形式的数据。

初始化时候的store，可以传入initialState作为初始化用的store. 也可以不传。

##todomvc
目录基本一致

初始化执行流程：
index.html->index.js->store(空(action)->render)->container(app.js)->components(head main)

###注意点：
babel-core polyfill 构建完整的es6运行环境 必须放在最开头

##todo-with-undo 操作的撤销与不撤销
redux-undo ActionCreators
dispatch(ActionCreators.undo())
dispatch(ActionCreators.redo())

##async异步加载
需要middleware与logger

更新两遍stroe，即ui, 第一遍的时候更新更新名称以及指示需要更新。
更新完随即检查指示，是否再异步更新。

初始化的store数据为空，但是有结构存在。根据reducer生成

```
执行过程：index.html->index.js->
store(
render->store{
  selectedReddit: 'reactjs',
  postsByReddit: {}
}
)
->containers(app.js,props:
  {
    selectedReddit: 'reactjs',
    posts: [],
    isFetching: true,
    lastUpdated: null
    dispatch: dispatch
  }
)
->dispatch(fetchPostsIfNeeded('reactjs')){
  ->dispatch(requestPosts(reddit))->render()(
    action: {'REQUEST_POSTS', 'reactjs'}
    newStore{
      selectedReddit: 'reactjs',
      postsByReddit: {
        'reactjs': {
          isFetching: true,
          didInvalidate: false,
          items: []
        }
      }
    }
  )
  ->更新ui(不变)

->dispatch(receivePosts('reactjs', json)) {
    action: {
      type: RECEIVE_POSTS,
      reddit: 'reactjs',
      posts: json.data.children.map(child => child.data),
      receivedAt: Date.now()
    }
    newState: {
      selectedReddit: 'reactjs',
      postsByReddit: {
        'reactjs': {
          isFetching: false,
          didInvalidate: false,
          items: posts,
          lastUpdated: receivedAt
        }
      }
    }
  }
  ->更新ui props {
    selectedReddit: 'reactjs',
    posts: json,
    isFetching: false,
    lastUpdated: receivedAt
  }
}
```
##universal
###执行流程：
```
server/server.js -> client/index.js -> common/store/configureStore.js({"counter":45})
{
reducers.js=>({"counter":45}, null)=>store{"counter":45};
}
->containers/app.js->component/counter.js

->dispatch(increment)->action:{type:INCREMENT_COUNTER}->reducers.js:
{
({"counter":45}, {type:INCREMENT_COUNTER}) => {"counter":45}
}
->containers/app.js->component/counter.js

->dispatch(incrementIfOdd)->dispatch(increment)->...
//thunk 中间件的实质就是可以在dispatch中加入其他的dispatch

->dispatch(incrementAsync)->setTimeout(dispatch(increment), 1000)->...
```
##real-wrold
###目录
d-actions
d-components
d-containers
d-middleware /api.js
d-reducers
d-store
routes.js /路由
server.js
index.js
index.html
package.json
webpack.config.js
d-node_modules

state结构
{
  entities: {},
  pagination: string,
  errorMessage: string,
  router: string,
}

###执行流程：
```
index.htnl->index.js(store=null)->container/root.js
->redux-router:path("/")->container/app.js: props
{
  errorMessage: string: null
  resetErrorMessage: func: {type: RESET_ERROR_MESSAGE}
  pushState: func: redux-router
  inputValue: string: state.router: " " 即url地址中路由部分
  children: node :null redux-router中注入的children，即user与reps部分
}
->component/Explore.js
->handleChange()->pushState(null, `/${nextValue}
```
