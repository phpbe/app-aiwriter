
<be-north>
    <div id="app-north">
        <div class="be-row">
            <div class="be-col">
                <div style="padding: 1.25rem 0 0 2rem;">
                    <el-link icon="el-icon-back" href="<?php echo $this->backUrl; ?>">返回加工任务列表</el-link>
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
            $formData['id'] = ($this->process ? $this->process->id : '');
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
                                <el-form-item style="margin: 0;" prop="name" :rules="[{required: true, message: '请输入加工任务名称', trigger: 'change' }]">
                                    <el-input
                                            type="text"
                                            placeholder="请输入加工任务名称"
                                            v-model = "formData.name"
                                            size="medium"
                                            maxlength="200"
                                            show-word-limit>
                                    </el-input>
                                </el-form-item>
                                <?php $formData['name'] = ($this->process ? $this->process->name : ''); ?>
                            </div>
                        </div>

                        <div class="be-row be-mt-100">
                            <div class="be-col-24 be-md-col-auto be-lh-250">
                                素材分类：
                            </div>
                            <div class="be-col-24 be-md-col-auto">
                                <div class="be-pl-50 be-pt-100"></div>
                            </div>
                            <div class="be-col-24 be-md-col">
                                <el-select v-model="formData.material_category_id" size="medium">
                                    <?php
                                    foreach ($this->materialCategoryKeyValues as $key => $val) {
                                        ?>
                                        <el-option label="<?php echo $val; ?>" value="<?php echo $key; ?>"></el-option>
                                        <?php
                                    }
                                    ?>
                                </el-select>
                                <?php
                                $formData['material_category_id'] = ($this->process ? $this->process->material_category_id : 'all');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="be-col-24 be-md-col-auto">
                    <div class="be-pl-150 be-pt-150"></div>
                </div>
                <div class="be-col-24 be-md-col-auto">
                    <div class="be-p-150 be-bc-fff" style="height: 100%;">
                        <div class="be-row">
                            <div class="be-col">是否启用：</div>
                            <div class="be-col-auto">
                                <el-switch v-model.number="formData.is_enable" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                            </div>
                        </div>
                        <?php $formData['is_enable'] = ($this->process ? $this->process->is_enable : 1); ?>
                    </div>
                </div>
            </div>


            <div class="be-mt-150 be-p-150 be-bc-fff">
                <div class="be-fs-110">素材加工</div>

                <div class="be-row be-mt-200">
                    <div class="be-col-24 be-md-col-auto">
                        标题：
                    </div>
                    <div class="be-col-24 be-md-col-auto">
                        <div class="be-pl-50 be-pt-100"></div>
                    </div>
                    <div class="be-col-24 be-md-col">
                        <el-radio v-model="formData.details.title.type" label="material">取用素材标题</el-radio>
                        <el-radio v-model="formData.details.title.type" label="ai">AI处理</el-radio>
                    </div>
                </div>

                <div class="be-mt-100" v-if="formData.details.title.type === 'ai'">

                    <div class="be-row">
                        <div class="be-col-24 be-md-col-auto">
                            系统提示语：
                        </div>
                        <div class="be-col-24 be-md-col">
                            <el-input
                                    type="textarea"
                                    :autosize="{minRows:4,maxRows:12}"
                                    placeholder="请输入AI处理系统提示语"
                                    v-model = "formData.details.title.ai_system_prompt"
                                    size="medium"
                                    maxlength="500"
                                    show-word-limit>
                            </el-input>
                        </div>
                        <div class="be-col-24 be-md-col-auto">
                            <div class="be-pl-100 be-pt-100"></div>
                        </div>
                        <div class="be-col-24 be-md-col">
                            使用模板：
                            <el-select @change="insertTemplate('title', 'system')" v-model = "template.title_system">
                                <?php
                                foreach ($this->titleSystemTemplates as $template) {
                                    echo '<el-option value="';
                                    echo $template;
                                    echo '">';
                                    echo $template;
                                    echo '</el-option>';
                                }
                                ?>
                            </el-select>
                        </div>
                    </div>

                    <div class="be-row be-mt-100">
                        <div class="be-col-24 be-md-col-auto">
                            <span class="be-c-red">*</span> 用户提示语：
                        </div>
                        <div class="be-col-24 be-md-col">
                            <el-input
                                    type="textarea"
                                    :autosize="{minRows:4,maxRows:12}"
                                    placeholder="请输入AI处理用户提示语"
                                    v-model = "formData.details.title.ai_user_prompt"
                                    size="medium"
                                    maxlength="500"
                                    show-word-limit>
                            </el-input>
                        </div>
                        <div class="be-col-24 be-md-col-auto">
                            <div class="be-pl-100 be-pt-100"></div>
                        </div>
                        <div class="be-col-24 be-md-col">
                            <div>
                                使用模板：
                                <el-select @change="insertTemplate('title', 'user')" v-model = "template.title_user">
                                    <?php
                                    foreach ($this->titleUserTemplates as $template) {
                                        echo '<el-option value="';
                                        echo $template;
                                        echo '">';
                                        echo $template;
                                        echo '</el-option>';
                                    }
                                    ?>
                                </el-select>
                            </div>
                            <div class="be-mt-100">
                                插入标签：
                                <el-button type="primary" size="mini" @click="insertTag('title', '{素材标题}')">{素材标题}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('title', '{素材摘要}')">{素材摘要}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('title', '{素材描述}')">{素材描述}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('title', '{素材备注1}')">{素材备注1}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('title', '{素材备注2}')">{素材备注2}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('title', '{素材备注3}')">{素材备注3}</el-button>
                            </div>
                        </div>
                    </div>

                </div>
                <?php
                if ($this->process) {
                    $formData['details'] = $this->process->details;
                } else {
                    $formData['details'] = [];
                    $formData['details']['title'] = [
                        'type' => 'ai',
                        'ai_system_prompt' => "",
                        'ai_user_prompt' => "",
                    ];
                }
                ?>

                <div class="be-row be-mt-300">
                    <div class="be-col-24 be-md-col-auto">
                        描述：
                    </div>
                    <div class="be-col-24 be-md-col-auto">
                        <div class="be-pl-50 be-pt-100"></div>
                    </div>
                    <div class="be-col-24 be-md-col">
                        <el-radio v-model="formData.details.description.type" label="material">取用素材描述</el-radio>
                        <el-radio v-model="formData.details.description.type" label="ai">AI处理</el-radio>
                    </div>
                </div>
                <div class="be-mt-100" v-if="formData.details.description.type === 'ai'">

                    <div class="be-row">
                        <div class="be-col-24 be-md-col-auto">
                            系统提示语：
                        </div>
                        <div class="be-col-24 be-md-col">
                            <el-input
                                    type="textarea"
                                    :autosize="{minRows:4,maxRows:12}"
                                    placeholder="请输入AI处理系统提示语"
                                    v-model = "formData.details.description.ai_system_prompt"
                                    size="medium"
                                    maxlength="500"
                                    show-word-limit>
                            </el-input>
                        </div>
                        <div class="be-col-24 be-md-col-auto">
                            <div class="be-pl-100 be-pt-100"></div>
                        </div>
                        <div class="be-col-24 be-md-col">
                            使用模板：
                            <el-select @change="insertTemplate('description', 'system')" v-model = "template.description_system">
                                <?php
                                foreach ($this->descriptionSystemTemplates as $template) {
                                    echo '<el-option value="';
                                    echo $template;
                                    echo '">';
                                    echo $template;
                                    echo '</el-option>';
                                }
                                ?>
                            </el-select>
                        </div>
                    </div>


                    <div class="be-row be-mt-100">
                        <div class="be-col-24 be-md-col-auto">
                            <span class="be-c-red">*</span> 用户提示语：
                        </div>
                        <div class="be-col-24 be-md-col">
                            <el-input
                                    type="textarea"
                                    :autosize="{minRows:4,maxRows:12}"
                                    placeholder="请输入AI处理提问内容"
                                    v-model = "formData.details.description.ai_user_prompt"
                                    size="medium"
                                    maxlength="500"
                                    show-word-limit>
                            </el-input>
                        </div>
                        <div class="be-col-24 be-md-col-auto">
                            <div class="be-pl-100 be-pt-100"></div>
                        </div>
                        <div class="be-col-24 be-md-col">
                            <div>
                                使用模板：
                                <el-select @change="insertTemplate('description', 'user')" v-model = "template.description_user">
                                    <?php
                                    foreach ($this->descriptionUserTemplates as $template) {
                                        echo '<el-option value="';
                                        echo $template;
                                        echo '">';
                                        echo $template;
                                        echo '</el-option>';
                                    }
                                    ?>
                                </el-select>
                            </div>
                            <div class="be-mt-100">
                                插入标签：
                                <el-button type="primary" size="mini" @click="insertTag('description', '{素材标题}')">{素材标题}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('description', '{素材摘要}')">{素材摘要}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('description', '{素材描述}')">{素材描述}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('description', '{素材备注1}')">{素材备注1}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('description', '{素材备注2}')">{素材备注2}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('description', '{素材备注3}')">{素材备注3}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('description', '{标题}')">{标题}</el-button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (!$this->process) {
                    $formData['details']['description'] = [
                        'type' => 'ai',
                        'ai_system_prompt' => "",
                        'ai_user_prompt' => "",
                    ];
                }
                ?>


                <div class="be-row be-mt-300">
                    <div class="be-col-24 be-md-col-auto">
                        摘要：
                    </div>
                    <div class="be-col-24 be-md-col-auto">
                        <div class="be-pl-50 be-pt-100"></div>
                    </div>
                    <div class="be-col-24 be-md-col">
                        <el-radio v-model="formData.details.summary.type" label="material">取用素材摘要</el-radio>
                        <el-radio v-model="formData.details.summary.type" label="extract">从最终描述中提取</el-radio>
                        <el-radio v-model="formData.details.summary.type" label="ai">AI处理</el-radio>
                    </div>
                </div>
                <div class="be-row be-mt-100" v-if="formData.details.summary.type === 'extract'">
                    <div class="be-col-24 be-md-col-auto be-lh-250">
                        提取长度：
                    </div>
                    <div class="be-col-24 be-md-col-auto">
                        <div class="be-pl-50 be-pt-100"></div>
                    </div>
                    <div class="be-col-24 be-md-col">
                        <el-input-number
                                :precision="0"
                                :step="1"
                                :max="500"
                                maxlength="6"
                                placeholder="请输入提取长度"
                                v-model.number="formData.details.summary.extract"
                                size="medium">
                        </el-input-number>
                        个汉字
                    </div>
                </div>
                <div class="be-mt-100" v-if="formData.details.summary.type === 'ai'">

                    <div class="be-row">
                        <div class="be-col-24 be-md-col-auto">
                            系统提示语：
                        </div>
                        <div class="be-col-24 be-md-col">
                            <el-input
                                    type="textarea"
                                    :autosize="{minRows:4,maxRows:12}"
                                    placeholder="请输入AI处理系统提示语"
                                    v-model = "formData.details.summary.ai_system_prompt"
                                    size="medium"
                                    maxlength="500"
                                    show-word-limit>
                            </el-input>
                        </div>
                        <div class="be-col-24 be-md-col-auto">
                            <div class="be-pl-100 be-pt-100"></div>
                        </div>
                        <div class="be-col-24 be-md-col">
                            使用模板：
                            <el-select @change="insertTemplate('summary', 'system')" v-model = "template.summary_system">
                                <?php
                                foreach ($this->summarySystemTemplates as $template) {
                                    echo '<el-option value="';
                                    echo $template;
                                    echo '">';
                                    echo $template;
                                    echo '</el-option>';
                                }
                                ?>
                            </el-select>
                        </div>
                    </div>

                    <div class="be-row be-mt-100">
                        <div class="be-col-24 be-md-col-auto">
                            <span class="be-c-red">*</span> 用户提示语：
                        </div>
                        <div class="be-col-24 be-md-col">
                            <el-input
                                    type="textarea"
                                    :autosize="{minRows:4,maxRows:12}"
                                    placeholder="请输入AI处理提问内容"
                                    v-model = "formData.details.summary.ai_user_prompt"
                                    size="medium"
                                    maxlength="500"
                                    show-word-limit>
                            </el-input>
                        </div>
                        <div class="be-col-24 be-md-col-auto">
                            <div class="be-pl-100 be-pt-100"></div>
                        </div>
                        <div class="be-col-24 be-md-col">
                            <div>
                                使用模板：
                                <el-select @change="insertTemplate('summary', 'user')" v-model = "template.summary_user">
                                    <?php
                                    foreach ($this->summaryUserTemplates as $template) {
                                        echo '<el-option value="';
                                        echo $template;
                                        echo '">';
                                        echo $template;
                                        echo '</el-option>';
                                    }
                                    ?>
                                </el-select>
                            </div>
                            <div class="be-mt-100">
                                插入标签：
                                <el-button type="primary" size="mini" @click="insertTag('summary', '{素材标题}')">{素材标题}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('summary', '{素材摘要}')">{素材摘要}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('summary', '{素材描述}')">{素材描述}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('summary', '{素材备注1}')">{素材备注1}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('summary', '{素材备注2}')">{素材备注2}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('summary', '{素材备注3}')">{素材备注3}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('summary', '{标题}')">{标题}</el-button>
                                <el-button type="primary" size="mini" @click="insertTag('summary', '{描述}')">{描述}</el-button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (!$this->process) {
                    $formData['details']['summary'] = [
                        'type' => 'extract',
                        'extract' => 120,
                        'ai_system_prompt' => "",
                        'ai_user_prompt' => "",
                    ];
                }
                ?>

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
                template: {
                    title_system: "",
                    title_user: "",
                    summary_system: "",
                    summary_user: "",
                    description_system: "",
                    description_user: "",
                },
                loading: false,
                t: false
                <?php
                echo $uiItems->getVueData();
                ?>
            },
            methods: {
                insertTemplate: function (type, role) {
                    let field = type + "_" + role;
                    if (role === "system") {
                        this.formData.details[type].ai_system_prompt = this.template[field].replace("<br>", "\n");
                    } else {
                        this.formData.details[type].ai_user_prompt = this.template[field].replace("<br>", "\n");
                    }
                    this.template[field] = "";
                },
                insertTag: function (e, tag) {
                    this.formData.details[e].ai_user_prompt += tag;
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
                                            _this.formData.id = responseData.process.id;
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
                },

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