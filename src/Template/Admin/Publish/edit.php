<be-head>
    <style>
        .el-form-item {
            margin-bottom: inherit;
        }

        .el-form-item__content {
            line-height: inherit;
        }

        .publish-form-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .publish-form-table td {
            padding: .4rem 0;
            vertical-align: middle;
        }

        .publish-item-header {
            color: #666;
            background-color: #EBEEF5;
            height: 3rem;
            line-height: 3rem;
            margin-bottom: .5rem;
        }

        .publish-item {
            background-color: #fff;
            border-bottom: #EBEEF5 1px solid;
            padding-top: .5rem;
            padding-bottom: .5rem;
            margin-bottom: 2px;
        }

        .publish-item-op {
            width: 40px;
            line-height: 2.5rem;
            text-align: center;
        }

    </style>
</be-head>


<be-north>
    <div id="app-north">
        <div class="be-row">
            <div class="be-col">
                <div style="padding: 1.25rem 0 0 2rem;">
                    <el-link icon="el-icon-back" href="<?php echo $this->backUrl; ?>">返回发布任务列表</el-link>
                </div>
            </div>
            <div class="be-col-auto">
                <div style="padding: .75rem 2rem 0 0;">
                    <el-button size="medium" :disabled="loading" @click="vueCenter.cancel();">取消</el-button>
                    <el-button type="success" size="medium" :disabled="loading" @click="vueCenter.save('stay');">仅保存</el-button>
                    <el-button type="primary" size="medium" :disabled="loading" @click="vueCenter.save('');">保存并返回</el-button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let vueNorth = new Vue({
            el: '#app-north',
            data: {
                loading: false,
            }
        });
    </script>
</be-north>


<be-page-content>
    <?php
    $formData = [];
    $uiItems = new \Be\AdminPlugin\UiItem\UiItems();
    $rootUrl = \Be\Be::getRequest()->getRootUrl();
    ?>

    <div id="app" v-cloak>
        <el-form ref="formRef" :model="formData" class="be-mb-400">
            <?php
            $formData['id'] = ($this->publish ? $this->publish->id : '');
            ?>

            <div class="be-row">
                <div class="be-col-24 be-md-col">
                    <div class="be-p-150 be-bc-fff">
                        <div class="be-row">
                            <div class="be-col-24 be-md-col-auto be-lh-250">
                                <span class="be-c-red">*</span> 名称：
                            </div>
                            <div class="be-col-24 be-md-col-auto">
                                <div class="be-pl-50 be-pt-100"></div>
                            </div>
                            <div class="be-col-24 be-md-col">
                                <el-form-item style="margin: 0;" prop="name" :rules="[{required: true, message: '请输入发布任务名称', trigger: 'change' }]">
                                    <el-input
                                            type="text"
                                            placeholder="请输入发布任务名称"
                                            v-model = "formData.name"
                                            size="medium"
                                            maxlength="200"
                                            show-word-limit>
                                    </el-input>
                                </el-form-item>
                                <?php $formData['name'] = ($this->publish ? $this->publish->name : ''); ?>
                            </div>
                        </div>

                        <div class="be-row be-mt-100">
                            <div class="be-col-24 be-md-col-auto be-lh-250">
                                <span class="be-c-red">*</span> 加工任务：
                            </div>
                            <div class="be-col-24 be-md-col-auto">
                                <div class="be-pl-50 be-pt-100"></div>
                            </div>
                            <div class="be-col-24 be-md-col">
                                <el-form-item style="margin: 0;" prop="process_id" :rules="[{required: true, message: '请选择加工任务', trigger: 'change' }]">
                                    <el-select v-model="formData.process_id" size="medium">
                                        <?php
                                        foreach ($this->processKeyValues as $key => $val) {
                                            ?>
                                            <el-option label="<?php echo $val; ?>" value="<?php echo $key; ?>"></el-option>
                                            <?php
                                        }
                                        ?>
                                    </el-select>
                                </el-form-item>
                                <?php
                                $formData['process_id'] = ($this->publish ? $this->publish->process_id : '');
                                ?>
                            </div>
                        </div>

                        <div class="be-row be-mt-100">
                            <div class="be-col-24 be-md-col-auto be-lh-250">
                                <span class="be-c-red">*</span> 发布网址：
                            </div>
                            <div class="be-col-24 be-md-col-auto">
                                <div class="be-pl-50 be-pt-100"></div>
                            </div>
                            <div class="be-col-24 be-md-col">
                                <el-form-item prop="post_url" :rules="[{required: true, message: '请输入发布网址', trigger: 'change' }]">
                                    <el-input
                                            type="text"
                                            placeholder="请输入发布网址"
                                            v-model="formData.post_url"
                                            maxlength="300"
                                            show-word-limit>
                                    </el-input>
                                </el-form-item>
                                <?php $formData['post_url'] = ($this->publish ? $this->publish->post_url : ''); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="be-col-24 be-md-col-auto">
                    <div class="be-pl-150 be-pt-150"></div>
                </div>
                <div class="be-col-24 be-md-col-auto">
                    <div class="be-p-150 be-bc-fff" style="height: 100%;">
                        <table>
                            <tr>
                                <td>是否启用：</td>
                                <td>
                                    <el-switch v-model.number="formData.is_enable" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                    <?php $formData['is_enable'] = ($this->publish ? $this->publish->is_enable : 1); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="be-pt-50">间隔时间（毫秒）：</td>
                                <td class="be-pt-50">
                                    <el-input-number v-model="formData.interval"></el-input-number>
                                    <?php $formData['interval'] = ($this->publish ? $this->publish->interval : 1000); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="be-pt-50">成功标记：</td>
                                <td class="be-pt-50">
                                    <el-input
                                            type="text"
                                            placeholder="请输入成功标记"
                                            v-model = "formData.success_mark"
                                            size="medium"
                                            maxlength="60"
                                            show-word-limit>
                                    </el-input>
                                    <?php $formData['success_mark'] = ($this->publish ? $this->publish->success_mark : '[OK]'); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>


            <div class="be-mt-150 be-p-150 be-bc-fff">
                <div class="be-fs-110 be-pb-50 be-bb-eee">请求头</div>

                <div class="be-row be-mt-100 publish-item-header" v-if="formData.post_headers.length > 0">
                    <div class="be-col">
                        <div class="be-pl-100">名称</div>
                    </div>
                    <div class="be-col-auto">
                        <div class="be-pl-100"></div>
                    </div>
                    <div class="be-col">
                        值
                    </div>
                    <div class="be-col-auto">
                        <div class="publish-item-op">
                            操作
                        </div>
                    </div>
                </div>


                <div class="be-row publish-item" v-for="header, headerIndex in formData.post_headers" :key="headerIndex">
                    <div class="be-col">
                        <el-input
                                type="text"
                                placeholder="请输入名称"
                                v-model = "header.name"
                                size="medium"
                                maxlength="300"
                                show-word-limit>
                        </el-input>
                    </div>
                    <div class="be-col-auto">
                        <div class="be-pl-100"></div>
                    </div>
                    <div class="be-col">
                        <el-input
                                type="text"
                                placeholder="请输入值"
                                v-model = "header.value"
                                size="medium"
                                maxlength="600"
                                show-word-limit>
                        </el-input>
                    </div>
                    <div class="be-col-auto">
                        <div class="publish-item-op">
                            <el-link type="danger" icon="el-icon-delete" @click="deleteHeader(header)"></el-link>
                        </div>
                    </div>
                </div>

                <el-button class="be-mt-100" size="small" type="primary" @click="addHeader">新增请求头</el-button>
                <?php
                if ($this->publish) {
                    $formData['post_headers'] = $this->publish->post_headers;
                } else {
                    $formData['post_headers'] = [];
                }
                ?>
            </div>


            <div class="be-mt-150 be-p-150 be-bc-fff">
                <div class="be-fs-110 be-pb-50 be-bb-eee">请求体</div>

                <div class="be-row be-mt-100">
                    <div class="be-col-auto">
                        请求格式：
                    </div>
                    <div class="be-col">
                        <el-radio v-model="formData.post_format" label="form">FORM 表单</el-radio>
                        <el-radio v-model="formData.post_format" label="json">JSON 数据</el-radio>
                    </div>
                </div>
                <?php $formData['post_format'] = ($this->publish ? $this->publish->post_format : 'form'); ?>

                <div class="be-row be-mt-100">
                    <div class="be-col-auto">
                        数据处理方法：
                    </div>
                    <div class="be-col">
                        <el-radio v-model="formData.post_data_type" label="mapping">映射</el-radio>
                        <el-radio v-model="formData.post_data_type" label="code">代码处理</el-radio>
                    </div>
                </div>
                <?php $formData['post_data_type'] = ($this->publish ? $this->publish->post_data_type : 'mapping'); ?>


                <div class="be-mt-100" v-show="formData.post_data_type === 'mapping'">

                    <div class="be-row be-mt-100 publish-item-header" v-if="formData.post_data_mapping.length > 0">
                        <div class="be-col">
                            <div class="be-pl-100">名称</div>
                        </div>
                        <div class="be-col-auto">
                            <div class="be-pl-100"></div>
                        </div>
                        <div class="be-col be-ta-center">
                            值类型
                        </div>
                        <div class="be-col-auto">
                            <div class="be-pl-100"></div>
                        </div>
                        <div class="be-col">
                            值
                        </div>
                        <div class="be-col-auto">
                            <div class="publish-item-op">
                                操作
                            </div>
                        </div>
                    </div>


                    <div class="be-row publish-item" v-for="mapping, mappingIndex in formData.post_data_mapping" :key="mappingIndex">
                        <div class="be-col">
                            <el-input
                                    type="text"
                                    placeholder="请输入名称"
                                    v-model = "mapping.name"
                                    size="medium"
                                    maxlength="300"
                                    show-word-limit>
                            </el-input>
                        </div>
                        <div class="be-col-auto">
                            <div class="be-pl-100"></div>
                        </div>
                        <div class="be-col be-ta-center be-lh-250">
                            <el-radio v-model="mapping.value_type" label="field">取用</el-radio>
                            <el-radio v-model="mapping.value_type" label="custom">自定义</el-radio>
                        </div>
                        <div class="be-col-auto">
                            <div class="be-pl-100"></div>
                        </div>
                        <div class="be-col">
                            <div v-show="mapping.value_type === 'field'">
                                <el-select v-model="mapping.field" size="medium">
                                    <el-option label="加工结果 - ID" value="id"></el-option>
                                    <el-option label="加工结果 - 标题" value="title"></el-option>
                                    <el-option label="加工结果 - 摘要" value="summary"></el-option>
                                    <el-option label="加工结果 - 描述" value="description"></el-option>
                                    <el-option label="加工结果 - 创建时间" value="create_time"></el-option>
                                    <el-option label="加工结果 - 更新时间" value="update_time"></el-option>

                                    <el-option label="素材 - ID" value="material.id"></el-option>
                                    <el-option label="素材 - 分类ID" value="material.category_id"></el-option>
                                    <el-option label="素材 - 唯一键" value="material.unique_key"></el-option>
                                    <el-option label="素材 - 标题" value="material.title"></el-option>
                                    <el-option label="素材 - 摘要" value="material.summary"></el-option>
                                    <el-option label="素材 - 描述" value="material.description"></el-option>
                                    <el-option label="素材 - 备注1" value="material.remark_1"></el-option>
                                    <el-option label="素材 - 备注2" value="material.remark_2"></el-option>
                                    <el-option label="素材 - 备注3" value="material.remark_3"></el-option>
                                    <el-option label="素材 - 备注4" value="material.remark_4"></el-option>
                                    <el-option label="素材 - 备注5" value="material.remark_5"></el-option>
                                    <el-option label="素材 - 备注6" value="material.remark_6"></el-option>
                                    <el-option label="素材 - 创建时间" value="material.create_time"></el-option>
                                    <el-option label="素材 - 更新时间" value="material.update_time"></el-option>
                                </el-select>
                            </div>
                            <div v-show="mapping.value_type === 'custom'">
                                <el-input
                                        type="text"
                                        placeholder="请输入自定义值"
                                        v-model = "mapping.custom"
                                        size="medium"
                                        maxlength="300"
                                        show-word-limit>
                                </el-input>
                            </div>
                        </div>
                        <div class="be-col-auto">
                            <div class="publish-item-op">
                                <el-link type="danger" icon="el-icon-delete" @click="deletePostDataMapping(mapping)"></el-link>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($this->publish) {
                        $formData['post_data_mapping'] = $this->publish->post_data_mapping;
                    } else {
                        $formData['post_data_mapping'] = [
                            [
                                'name' => 'unique_key',
                                'value_type' => 'field',
                                'field' => 'material.unique_key',
                                'custom' => '',
                            ],
                            [
                                'name' => 'title',
                                'value_type' => 'field',
                                'field' => 'title',
                                'custom' => '',
                            ],
                            [
                                'name' => 'summary',
                                'value_type' => 'field',
                                'field' => 'summary',
                                'custom' => '',
                            ],
                            [
                                'name' => 'description',
                                'value_type' => 'field',
                                'field' => 'description',
                                'custom' => '',
                            ],
                        ];
                    }
                    ?>

                    <el-button class="be-mt-100" size="small" type="primary" @click="addPostDataMapping">新增字段</el-button>

                </div>


                <div class="be-mt-100" v-show="formData.post_data_type === 'code'">

                    <div class="be-row">
                        <div class="be-col">
                            <pre>function (object $row, object $material) {</pre>
                            <?php
                            $driver = new \Be\AdminPlugin\Form\Item\FormItemCode([
                                'name' => 'post_data_code',
                                'language' => 'php',
                            ]);
                            echo $driver->getHtml();

                            if ($this->publish) {
                                $formData['post_data_code'] = $this->publish->post_data_code;
                            } else {

                                $code = '$arr = [];' . "\n";
                                $code .= '$arr[\'unique_key\'] = $material->unique_key;' . "\n";
                                $code .= '$arr[\'title\'] = $row->title;' . "\n";
                                $code .= '$arr[\'summary\'] = $row->summary;' . "\n";
                                $code .= '$arr[\'description\'] = $row->description;' . "\n";
                                $code .= "\n";
                                $code .= 'return $arr;' . "\n";

                                $formData['post_data_code'] = $code;
                            }

                            $uiItems->add($driver);
                            ?>
                            <pre>}</pre>
                        </div>
                        <div class="be-col-auto">
                            <div class="be-pl-100"></div>
                        </div>
                        <div class="be-col">
                            参数 $row 为加工好的数据，结构如下：
                            <pre><?php
                            $row = (object)[
                                'id' => '00e9e677-b801-11ed-a779-04d9f5f8b7ed',
                                'process_id' => 'cb1cde02-b761-11ed-bdf5-04d9f5f8b7ed',
                                'material_id' => '0f46d705-b74c-11ed-8b1f-04d9f5f8b7ed',
                                'title' => '标题',
                                'summary' => '摘要',
                                'description' => '描述',
                                'create_time' => '2023-03-01 15:16:36',
                                'update_time' => '2023-03-01 15:17:12',
                            ];
                            print_r($row);
                            ?></pre>

                            参数 $material 为原始素材，结构如下：
                            <pre><?php
                            $material = (object)[
                                'id' => 'b2786be4-af66-11ed-b252-04d9f5f8b7ed',
                                'category_id' => 'c6c9ffca-af3a-11ed-b252-04d9f5f8b7ed',
                                'unique_key' => '唯一键',
                                'title' => '标题',
                                'summary' => '摘要',
                                'description' => '描述',
                                'remark_1' => '备注1',
                                'remark_2' => '备注2',
                                'remark_3' => '备注3',
                                'remark_4' => '备注4',
                                'remark_5' => '备注5',
                                'remark_6' => '备注6',
                                'create_time' => '2023-02-18 16:31:50',
                                'update_time' => '2023-02-18 16:31:50',
                            ];
                            print_r($material);
                            ?></pre>
                        </div>
                    </div>

                </div>

            </div>
        </el-form>
    </div>

    <?php
    echo $uiItems->getJs();
    echo $uiItems->getCss();
    ?>

    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,

                loading: false,
                t: false
                <?php
                echo $uiItems->getVueData();
                ?>
            },
            methods: {
                addHeader() {
                    this.formData.post_headers.push({
                       name: "",
                       value: "",
                    });
                },
                deleteHeader(header) {
                    this.formData.post_headers.splice(this.formData.post_headers.indexOf(header), 1);
                },

                addPostDataMapping() {
                    this.formData.post_data_mapping.push({
                        name: "",
                        value_type: "custom",
                        field: "",
                        custom: "",
                    });
                },
                deletePostDataMapping(mapping) {
                    this.formData.post_data_mapping.splice(this.formData.post_data_mapping.indexOf(mapping), 1);
                },

                save: function (command) {
                    let _this = this;
                    this.$refs["formRef"].validate(function (valid) {
                        if (valid) {
                            _this.loading = true;
                            vueNorth.loading = true;
                            _this.$http.post("<?php echo $this->formActionUrl; ?>", {
                                formData: _this.formData
                            }).then(function (response) {
                                _this.loading = false;
                                vueNorth.loading = false;
                                //console.log(response);
                                if (response.status === 200) {
                                    var responseData = response.data;
                                    if (responseData.success) {
                                        _this.$message.success(responseData.message);

                                        if (command === 'stay') {
                                            _this.formData.id = responseData.publish.id;
                                        } else {
                                            setTimeout(function () {
                                                window.onbeforeunload = null;
                                                window.location.href = responseData.redirectUrl;
                                            }, 1000);
                                        }

                                    } else {
                                        if (responseData.message) {
                                            _this.$message.error(responseData.message);
                                        } else {
                                            _this.$message.error("服务器返回数据异常！");
                                        }
                                    }
                                }
                            }).catch(function (error) {
                                _this.loading = false;
                                vueNorth.loading = false;
                                _this.$message.error(error);
                            });
                        } else {
                            return false;
                        }
                    });
                },
                cancel: function () {
                    window.onbeforeunload = null;
                    window.location.href = "<?php echo $this->backUrl; ?>";
                }

                <?php
                echo $uiItems->getVueMethods();
                ?>
            }

            <?php
            $uiItems->setVueHook('mounted', 'window.onbeforeunload = function(e) {e = e || window.event; if (e) { e.returnValue = ""; } return ""; };');
            echo $uiItems->getVueHooks();
            ?>
        });
    </script>
</be-page-content>